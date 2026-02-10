<?php

namespace App\Http\Controllers\Api;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonRequest;
use App\Http\Resources\PersonResource;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Middleware\SetCommunityContextAPI;
use Illuminate\Routing\Controllers\HasMiddleware;

class PersonController extends Controller
{

    public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth:sanctum');
        $this->middleware(SetCommunityContextAPI::class);
        $this->middleware(function ($request, $next) {

            $communityId = $request->input('community') ?? $request->input('community_id');

            if ($communityId) {
                setPermissionsTeamId($communityId);
            }
            return $next($request);
        });


        // Middleware pour permissions CRUD
        $table = Person::getTableName();
        // $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show', 'index']);
        $this->middleware("permission:create $table")->only(['create', 'store', 'push']);
        $this->middleware("permission:update $table")->only(['edit', 'update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }



    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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

        $query = Person::query()
            ->when($communityId, function ($query) use ($communityId) {
                $query->whereHas('personRoles', function ($q) use ($communityId) {
                    $q->whereHas('animal', function ($q) use ($communityId) {
                        $q->whereHas('premise', function ($q2) use ($communityId) {
                            $q2->where('community_id', $communityId);
                        });
                    });
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
            'data' => PersonResource::collection($items),
            'cursor' => $nextCursor,
            'has_more' => $hasMore,
            'server_time' => now()->toDateTimeString()
        ]);
    }

    function push(Request $request)
    {
        Log::info("Received Data for pushing ", $request->all());
        $request->validate([
            'data' => 'required|array',
            'data.*.uid' => 'required|string',
            'data.*.version' => 'required|integer',
            'data.*.deleted_at' => 'nullable|date'
        ]);

        $applied = [];
        $conflicts = [];
        $errors = [];

        foreach ($request->input('data', []) as $personData) {
            $uid = $personData['uid'] ?? null;
            try {
                $validator = Validator::make($personData, [
                    'uid' => 'required|string',
                    'version' => 'required|integer',
                    'name' => 'required|string|max:255',
                    'address' => 'nullable|string|max:1000',
                    'phone' => 'nullable|string|max:50',
                    'national_id' => 'required|string|max:100',
                ]);

                if ($validator->fails()) {
                    $errors[] = [
                        'uid' => $uid,
                        'code' => 'VALIDATION_ERROR',
                        'message' => $validator->errors()->first()
                    ];
                    continue;
                }

                DB::beginTransaction();
                $existing = Person::where('uid', $uid)->first();

                if (!empty($personData['deleted_at'])) {
                    if ($existing) {
                        if ($personData['version'] <= $existing->version) {
                            $conflicts[] = [
                                'uid' => $uid,
                                'server_data' => new PersonResource($existing)
                            ];
                            DB::rollBack();
                            continue;
                        }

                        $existing->deleted_at = $personData['deleted_at'];
                        $existing->version = $personData['version'];
                        $existing->save();
                    }

                    $applied[] = $uid;
                    DB::commit();
                    continue;
                }

                if (!$existing) {
                    $person = Person::create([
                        'uid' => $uid,
                        'version' => $personData['version'],
                        'name' => $personData['name'] ?? null,
                        'address' => $personData['address'] ?? null,
                        'phone' => $personData['phone'] ?? null,
                        'nationalId' => $personData['national_id'] ?? null,
                        'created_by' => auth()->id(),
                    ]);
                    $applied[] = $uid;
                    DB::commit();
                    continue;
                }

                $serverVersion = (int) ($existing->version ?? 0);
                $clientVersion = (int) $personData['version'];

                if ($clientVersion <= $serverVersion) {
                    $conflicts[] = [
                        'uid' => $uid,
                        'server_data' => new PersonResource($existing)
                    ];
                    DB::rollBack();
                    continue;
                }

                $existing->fill([
                    'version' => $clientVersion,
                    'name' => $personData['name'] ?? $existing->name,
                    'address' => $personData['address'] ?? $existing->address,
                    'phone' => $personData['phone'] ?? $existing->phone,
                    'nationalId' => $personData['nationalId'] ?? $existing->nationalId,
                ]);
                $existing->save();
                $applied[] = $uid;
                DB::commit();

            } catch (QueryException $qe) {
                DB::rollBack();
                $errors[] = [
                    'uid' => $uid,
                    'code' => 'UNIQUE_CONSTRAINT',
                    'message' => $qe->getMessage(),
                ];
            } catch (\Throwable $e) {
                DB::rollBack();
                $errors[] = [
                    'uid' => $uid,
                    'code' => 'UNKNOWN_ERROR',
                    'message' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'status' => 'OK',
            'applied' => $applied,
            'conflicts' => $conflicts,
            'errors' => $errors,
            'server_time' => now()->toDateTimeString()
        ]);
    }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(PersonRequest $request): JsonResponse
    // {
    //     $person = Person::create($request->validated());

    //     return response()->json(new PersonResource($person));
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(Person $person): JsonResponse
    // {
    //     return response()->json(new PersonResource($person));
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(PersonRequest $request, Person $person): JsonResponse
    // {
    //     $person->update($request->validated());

    //     return response()->json(new PersonResource($person));
    // }

    // /**
    //  * Delete the specified resource.
    //  */
    // public function destroy(Person $person): Response
    // {
    //     $person->delete();

    //     return response()->noContent();
    // }
}
