<?php

namespace App\Http\Controllers\Api;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Http\Resources\PersonResource;
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
        $since = $request->validate([
            'since' => 'nullable|date_format:Y-m-d H:i:s',
        ])['since'] ?? "1970-01-01 00:00:00";
        $persons = Person::query()
            ->when($communityId, function ($query) use ($communityId) {
                $query->whereHas('personRoles', function ($q) use ($communityId) {
                    $q->whereHas('animal', function ($q) use ($communityId) {
                        $q->whereHas('premise', function ($q) use ($communityId) {
                            $q->where('community_id', $communityId);
                        });
                    });
                });
            })
            ->when($since, function ($query) use ($since) {
                $query->where(function ($q) use ($since) {
                    $q->where('created_at', '>=', $since)
                        ->orWhere('updated_at', '>=', $since);
                });
            })
            ->paginate(100);
        $resource = PersonResource::collection($persons);
        $result = $resource->response()->getData(true);
        // si on est à la derniere page , on ajoute les last_synced_at
        if ($persons->currentPage() >= $persons->lastPage()) {
            $result['last_synced_at'] = now()->toDateTimeString();
        }

        return response()->json($result);
    }

    function push(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
            'data.*.uid' => 'required|string',
            'data.*.version' => 'required|integer',
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
                    'nationalId' => 'required|string|max:100',
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

                if (!$existing) {
                    // create new
                    $person = Person::create([
                        'uid' => $uid,
                        'version' => $personData['version'],
                        'name' => $personData['name'] ?? null,
                        'address' => $personData['address'] ?? null,
                        'phone' => $personData['phone'] ?? null,
                        'nationalId' => $personData['nationalId'] ?? null,
                    ]);
                    $applied[] = $uid;
                    DB::commit();
                    continue;
                }

                // existing found: conflict resolution by version
                $serverVersion = (int) ($existing->version ?? 0);
                $clientVersion = (int) $personData['version'];

                if ($serverVersion >= $clientVersion) {
                    // server has newer data -> conflict
                    $conflicts[] = [
                        'uid' => $uid,
                        'server_data' => (new PersonResource($existing))->response()->getData(true)
                    ];
                    DB::rollBack();
                    continue;
                }

                // client is newer  -> apply update
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
            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = [
                    'uid' => $uid,
                    'code' => 'UNKNOWN_ERROR',
                    'message' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'statut' => 'OK',
            'applied' => $applied,
            'conflicts' => $conflicts,
            'errors' => $errors,
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
