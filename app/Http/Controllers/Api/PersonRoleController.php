<?php

namespace App\Http\Controllers\Api;

use App\Models\Animal;
use App\Models\Person;
use App\Models\PersonRole;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PersonRoleResource;
use App\Http\Middleware\SetCommunityContextAPI;

class PersonRoleController extends Controller
{
    public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth:sanctum');
        $this->middleware(SetCommunityContextAPI::class);


        // Middleware pour permissions CRUD
        $table = Person::getTableName();
        // $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show', 'getAllData']);
        $this->middleware("permission:create $table")->only(['create', 'store']);
        $this->middleware("permission:update $table")->only(['edit', 'update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $communityId = getPermissionsTeamId();

        $validated = $request->validate([
            'cursor.updated_at' => 'nullable|date_format:Y-m-d H:i:s',
            'cursor.uid' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:200'
        ]);

        $limit = $validated['limit'] ?? 100;
        $cursorUpdatedAt = $validated['cursor']['updated_at'] ?? null;
        $cursorUid = $validated['cursor']['uid'] ?? null;

        $query = PersonRole::query()
            ->whereHas('animal', function ($q) use ($communityId) {
                $q->whereHas('premise', function ($q2) use ($communityId) {
                    $q2->where('community_id', $communityId);
                });
            })
            ->orderBy('updated_at')
            ->orderBy('uid');

        if ($cursorUpdatedAt && $cursorUid) {
            $query->where(function ($q) use ($cursorUpdatedAt, $cursorUid) {
                $q->where('updated_at', '>', $cursorUpdatedAt)
                    ->orWhere(function ($q2) use ($cursorUpdatedAt, $cursorUid) {
                        $q2->where('updated_at', $cursorUpdatedAt)
                            ->where('uid', '>', $cursorUid);
                    });
            });
        }

        $items = $query->limit($limit + 1)->get();

        $hasMore = $items->count() > $limit;
        $items = $items->take($limit);

        $nextCursor = null;
        if ($items->isNotEmpty()) {
            $last = $items->last();
            $nextCursor = [
                'updated_at' => $last->updated_at->toDateTimeString(),
                'uid' => $last->uid
            ];
        }

        return response()->json([
            'data' => PersonRoleResource::collection($items),
            'cursor' => $nextCursor,
            'has_more' => $hasMore,
            'server_time' => now()->toDateTimeString()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function push(Request $request): JsonResponse
    {

        $request->validate([
            'data' => 'required|array',
            'data.*.uid' => 'required|string',
            'data.*.version' => 'required|integer',
            'data.*.deleted_at' => 'nullable|date'
        ]);

        $applied = [];
        $conflicts = [];
        $errors = [];

        foreach ($request->data as $item) {
            DB::beginTransaction();
            try {
                $validator = Validator::make($item, [
                    'uid' => 'required|string',
                    'version' => 'required|integer',
                    'person_uid' => 'required|string',
                    'animal_uid' => 'required|string',
                    'role_type' => 'required|string',
                ]);

                if ($validator->fails()) {
                    $errors[] = ['uid' => $item['uid'] ?? null, 'code' => 'VALIDATION_ERROR', 'message' => $validator->errors()->first()];
                    DB::rollBack();
                    continue;
                }

                $existing = PersonRole::where('uid', $item['uid'])->first();
                $person = Person::where('uid', $item['person_uid'])->first();
                $animal = Animal::where('uid', $item['animal_uid'])->first();
                if (!$person || !$animal) {
                    DB::rollBack();
                    $errors[] = ['uid' => $item['uid'] ?? null, 'code' => 'MISSING_RELATION', 'message' => 'Person or Animal not found'];
                    continue;
                }

                if (!empty($item['deleted_at'])) {
                    if ($existing) {
                        if ($item['version'] <= $existing->version) {
                            $conflicts[] = ['uid' => $item['uid'], 'server_data' => new PersonRoleResource($existing)];
                            DB::rollBack();
                            continue;
                        }

                        $existing->deleted_at = $item['deleted_at'];
                        $existing->version = $item['version'];
                        $existing->save();
                    }

                    $applied[] = $item['uid'];
                    DB::commit();
                    continue;
                }

                if (!$existing) {
                    PersonRole::create([
                        'uid' => $item['uid'],
                        'version' => $item['version'],
                        'person_id' => $person->id,
                        'animal_id' => $animal->id,
                        'role_type' => $item['role_type'],
                    ]);
                    $applied[] = $item['uid'];
                    DB::commit();
                    continue;
                }

                $serverVersion = (int) ($existing->version ?? 0);
                $clientVersion = (int) $item['version'];
                if ($clientVersion <= $serverVersion) {
                    $conflicts[] = ['uid' => $item['uid'], 'server_data' => new PersonRoleResource($existing)];
                    DB::rollBack();
                    continue;
                }

                $existing->fill([
                    'version' => $clientVersion,
                    'person_id' => $person->id,
                    'animal_id' => $animal->id,
                    'role_type' => $item['role_type'],
                ]);
                $existing->save();
                $applied[] = $item['uid'];
                DB::commit();
            } catch (QueryException $qe) {
                DB::rollBack();
                $errors[] = ['uid' => $item['uid'] ?? null, 'code' => 'UNIQUE_CONSTRAINT', 'message' => $qe->getMessage()];
            } catch (\Throwable $e) {
                DB::rollBack();
                $errors[] = ['uid' => $item['uid'] ?? null, 'code' => 'SERVER_ERROR', 'message' => $e->getMessage()];
            }
        }

        return response()->json(['status' => 'OK', 'applied' => $applied, 'conflicts' => $conflicts, 'errors' => $errors, 'server_time' => now()->toDateTimeString()]);
    }
}
