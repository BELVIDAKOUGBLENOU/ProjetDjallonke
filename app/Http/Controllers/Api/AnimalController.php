<?php

namespace App\Http\Controllers\Api;

use App\Models\Animal;
use App\Models\Premise;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnimalRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AnimalResource;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Middleware\SetCommunityContextAPI;
use Illuminate\Routing\Controllers\HasMiddleware;

class AnimalController extends Controller
    // implements HasMiddleware
{
    public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth:sanctum');
        $this->middleware(SetCommunityContextAPI::class);
        $this->middleware(function ($request, $next) {
            $animalId = $request->route('api_animal');
            if ($animalId) {
                $animal = ($animalId instanceof Animal) ? $animalId : Animal::findOrFail($animalId);
                setPermissionsTeamId($animal->community_id);
            }
            return $next($request);
        });


        // Middleware pour permissions CRUD
        $table = Animal::getTableName();
        // $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show', 'getAllData']);
        $this->middleware("permission:create $table")->only(['create', 'store']);
        $this->middleware("permission:update $table")->only(['edit', 'update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }

    // public function getAllData(Request $request): JsonResponse
    // {
    //     $communityId = getPermissionsTeamId();
    //     $animals = Animal::query()
    //         ->whereHas('premise', function ($q) use ($communityId) {
    //             $q->where('community_id', $communityId);
    //         })
    //         ->get();

    //     return response()->json(AnimalResource::collection($animals));
    // }

    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $communityId = getPermissionsTeamId();
    //     $since = $request->validate([
    //         'since' => 'nullable|date_format:Y-m-d H:i:s',
    //     ])['since'] ?? "1970-01-01 00:00:00";
    //     $animals = Animal::
    //         whereHas('premise', function ($q) use ($communityId) {
    //             $q->where('community_id', $communityId);
    //         })->
    //         when($since, function ($query, $since) {
    //             $query->where('updated_at', '>=', $since);
    //         })->
    //         paginate();

    //     $resource = AnimalResource::collection($animals);
    //     $result = $resource->response()->getData(true);
    //     // si on est à la derniere page , on ajoute les last_synced_at
    //     if ($animals->currentPage() >= $animals->lastPage()) {
    //         $result['last_synced_at'] = now()->toDateTimeString();
    //     }

    //     return response()->json($result);
    // }

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

        $query = Animal::query()
            ->whereHas(
                'premise',
                fn($q) =>
                $q->where('community_id', $communityId)
            )
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
            'data' => AnimalResource::collection($items),
            'cursor' => $nextCursor,
            'has_more' => $hasMore,
            'server_time' => now()->toDateTimeString()
        ]);
    }


    // public function push(Request $request)
    // {
    //     $request->validate([
    //         'data' => 'required|array',
    //         'data.*.uid' => 'required|string',
    //         'data.*.version' => 'required|integer',
    //     ]);

    //     $applied = [];
    //     $conflicts = [];
    //     $errors = [];

    //     foreach ($request->input('data', []) as $item) {
    //         $uid = $item['uid'] ?? null;
    //         try {
    //             $validator = Validator::make($item, [
    //                 'uid' => 'required|string',
    //                 'version' => 'required|integer',
    //                 'species' => 'nullable|string',
    //                 'sex' => 'nullable|string',
    //                 'birth_date' => 'nullable|date',
    //                 'life_status' => 'nullable|string',
    //                 'premise_uid' => 'nullable|string',
    //             ]);

    //             if ($validator->fails()) {
    //                 $errors[] = ['uid' => $uid, 'code' => 'VALIDATION_ERROR', 'message' => $validator->errors()->first()];
    //                 continue;
    //             }

    //             DB::beginTransaction();
    //             $existing = Animal::where('uid', $uid)->first();

    //             if (!$existing) {
    //                 $data = $validator->validated();
    //                 if (!empty($data['premise_uid'])) {
    //                     $prem = Premise::where('uid', $data['premise_uid'])->first();
    //                     if ($prem)
    //                         $data['premises_id'] = $prem->id;
    //                     unset($data['premise_uid']);
    //                 }
    //                 $data['created_by'] = auth()->id();
    //                 Animal::create($data + ['version' => $item['version']]);
    //                 $applied[] = $uid;
    //                 DB::commit();
    //                 continue;
    //             }

    //             $serverVersion = (int) ($existing->version ?? 0);
    //             $clientVersion = (int) $item['version'];
    //             if ($clientVersion <= $serverVersion) {
    //                 $conflicts[] = ['uid' => $uid, 'server_data' => (new AnimalResource($existing))->response()->getData(true)];
    //                 DB::rollBack();
    //                 continue;
    //             }

    //             $data = $validator->validated();
    //             if (!empty($data['premise_uid'])) {
    //                 $prem = Premise::where('uid', $data['premise_uid'])->first();
    //                 if ($prem)
    //                     $data['premises_id'] = $prem->id;
    //                 unset($data['premise_uid']);
    //             }
    //             $existing->fill(array_merge($data, ['version' => $clientVersion]));
    //             $existing->save();
    //             $applied[] = $uid;
    //             DB::commit();
    //         } catch (QueryException $qe) {
    //             DB::rollBack();
    //             $errors[] = ['uid' => $uid, 'code' => 'UNIQUE_CONSTRAINT', 'message' => $qe->getMessage()];
    //         } catch (\Exception $e) {
    //             DB::rollBack();
    //             $errors[] = ['uid' => $uid, 'code' => 'UNKNOWN_ERROR', 'message' => $e->getMessage()];
    //         }
    //     }

    //     return response()->json(['statut' => 'OK', 'applied' => $applied, 'conflicts' => $conflicts, 'errors' => $errors]);
    // }
    public function push(Request $request): JsonResponse
    {
        // Le middleware a déjà fixé le contexte communauté
        $communityId = getPermissionsTeamId();

        $request->validate([
            'data' => 'required|array',
            'data.*.uid' => 'required|string',
            'data.*.version' => 'required|integer',
            'data.*.deleted_at' => 'nullable|date'
        ]);

        $applied = [];
        $conflicts = [];
        $errors = [];
        $user_id = Auth::user()->id;

        foreach ($request->data as $item) {
            DB::beginTransaction();

            try {
                $existing = Animal::where('uid', $item['uid'])->first();

                /* =========================
                 * DELETE (soft delete)
                 * ========================= */
                if (!empty($item['deleted_at'])) {
                    if ($existing) {
                        if ($item['version'] <= $existing->version) {
                            $conflicts[] = [
                                'uid' => $item['uid'],
                                'server_data' => new AnimalResource($existing)
                            ];
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

                /* =========================
                 * CREATE
                 * ========================= */
                if (!$existing) {
                    $animal = new Animal();
                    $animal->uid = $item['uid'];
                    $animal->fill(
                        $this->mapAnimalData($item, $communityId)
                    );
                    $animal->version = $item['version'];
                    $animal->created_by = $user_id;
                    $animal->save();

                    $applied[] = $item['uid'];
                    DB::commit();
                    continue;
                }

                /* =========================
                 * CONFLICT
                 * ========================= */
                if ($item['version'] <= $existing->version) {
                    $conflicts[] = [
                        'uid' => $item['uid'],
                        'server_data' => new AnimalResource($existing)
                    ];
                    DB::rollBack();
                    continue;
                }

                /* =========================
                 * UPDATE
                 * ========================= */
                $existing->fill(
                    $this->mapAnimalData($item, $communityId)
                );
                $existing->version = $item['version'];
                $existing->save();

                $applied[] = $item['uid'];
                DB::commit();

            } catch (\Throwable $e) {
                DB::rollBack();
                $errors[] = [
                    'uid' => $item['uid'],
                    'code' => 'SERVER_ERROR',
                    'message' => $e->getMessage()
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

    /**
     * Map the input data to the Animal model attributes.
     *
     * @param array $item
     * @param int|string $communityId
     * @return array
     */
    private function mapAnimalData(array $item, $communityId): array
    {
        $data = [
            'species' => $item['species'] ?? null,
            'sex' => $item['sex'] ?? null,
            'birth_date' => $item['birth_date'] ?? null,
            'life_status' => $item['life_status'] ?? null,
            'sex' => !is_null($item['Sex']) ?? strtoupper(substr($item['sex'], 0, 1))
        ];

        // Resolve premise_uid to premises_id
        if (!empty($item['premise_uid'])) {
            $premise = Premise::where('uid', $item['premise_uid'])
                ->where('community_id', $communityId)
                ->first();

            if ($premise) {
                $data['premises_id'] = $premise->id;
            }
        }

        return $data;
    }
}
