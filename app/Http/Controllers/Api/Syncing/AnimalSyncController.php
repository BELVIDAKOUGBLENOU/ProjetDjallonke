<?php

namespace App\Http\Controllers\Api\Syncing;

use App\Models\Animal;
use App\Models\Premise;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
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

class AnimalSyncController extends Controller
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

    public function pull(Request $request): JsonResponse
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
            ->with('premise')
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
            $uid = $item['uid'] ?? null;
            DB::beginTransaction();
            // validation des données de base
            $validator = Validator::make($item, [
                'uid' => 'required|string',
                'version' => 'required|integer',
                'sex' => 'required|string|in:M,F,MALE,FEMALE',
                'species' => 'required|string|in:OVINE,CAPRINE',
                'birth_date' => 'nullable|date',
                'life_status' => 'required|string|in:ALIVE,DEAD,SOLD',
                'premises_uid' => [
                    'required',
                    'string',
                    Rule::exists('premises', 'uid')->where(function ($query) use ($communityId) {
                        $query->where('community_id', $communityId);
                    })
                ],
            ], [
                'premises_uid.exists' => 'The specified premises does not exist in the community.'
            ]);


            if ($validator->fails()) {
                $errors[] = [
                    'uid' => $uid,
                    'code' => 'VALIDATION_ERROR',
                    'message' => $validator->errors()->first()
                ];
                continue;
            }

            try {
                $existing = Animal::where('uid', $uid)->first();

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
            'sex' => !is_null($item['sex']) ? strtoupper(substr($item['sex'], 0, 1)) : null
        ];

        // Resolve premises_uid to premises_id
        if (!empty($item['premises_uid'])) {
            $premise = Premise::where('uid', $item['premises_uid'])
                ->where('community_id', $communityId)
                ->first();

            if ($premise) {
                $data['premises_id'] = $premise->id;
            }
        }

        return $data;
    }
}
