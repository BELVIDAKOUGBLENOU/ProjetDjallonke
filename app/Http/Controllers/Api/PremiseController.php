<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Premise;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PremiseRequest;
use App\Http\Resources\PremiseResource;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Middleware\SetCommunityContextAPI;
use Illuminate\Routing\Controllers\HasMiddleware;

class PremiseController extends Controller
{

    public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth:sanctum');
        $this->middleware(SetCommunityContextAPI::class);
        $this->middleware(function ($request, $next) {
            $premisesId = $request->route('api_premise');
            if (!$premisesId) {
                return $next($request);
            }
            $premise = ($premisesId instanceof Premise) ? $premisesId : Premise::findOrFail($premisesId);

            if ($premise) {
                setPermissionsTeamId($premise->community_id);
            }
            return $next($request);
        });


        // Middleware pour permissions CRUD
        $table = Premise::getTableName();
        // $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show']);
        $this->middleware("permission:create $table")->only(['create', 'store']);
        $this->middleware("permission:update $table")->only(['edit', 'update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }
    public function getAllData(Request $request): JsonResponse
    {
        $premises = Premise::where('community_id', getPermissionsTeamId())->get();

        return response()->json(PremiseResource::collection($premises));
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

        $query = Premise::query()
            ->where('community_id', $communityId)
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
            'data' => PremiseResource::collection($items),
            'cursor' => $nextCursor,
            'has_more' => $hasMore,
            'server_time' => now()->toDateTimeString()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(PremiseRequest $request): JsonResponse
    // {
    //     $all = $request->validated();
    //     $all['community_id'] = getPermissionsTeamId();
    //     $all['created_by'] = auth()->id();
    //     $premise = Premise::create($all);

    //     return response()->json(new PremiseResource($premise));
    // }

    /**
     * Display the specified resource.
     */
    // public function show(Premise $premise): JsonResponse
    // {
    //     return response()->json(new PremiseResource($premise));
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(PremiseRequest $request, Premise $premise): JsonResponse
    // {
    //     $premise->update($request->validated());

    //     return response()->json(new PremiseResource($premise));
    // }

    // /**
    //  * Delete the specified resource.
    //  */
    // public function destroy(Premise $premise): Response
    // {
    //     $premise->delete();

    //     return response()->noContent();
    // }

    // public function syncPremises(Request $request): JsonResponse
    // {
    //     $this->middleware(['auth:sanctum', SetCommunityContextAPI::class, "can:create " . Premise::getTableName()]);
    //     $v1 = Validator::make($request->all(), [
    //         'premises' => 'required|array',
    //         "premises.*.uid" => 'required|string',
    //     ]);
    //     if ($v1->fails()) {
    //         return response()->json([
    //             'status' => 'ERROR',
    //             'errors' => $v1->errors()->messages(),
    //         ], 400);
    //     }
    //     $failedValidations = [];
    //     $rules = [

    //         'village_id' => 'required|integer|exists:villages,id',
    //         'code' => 'required|string|unique:premises,code',
    //         'address' => 'required|string|max:255',
    //         'gps_coordinates' => 'required|string|max:255',
    //         'health_status' => 'nullable|string|max:255',
    //         'type' => 'required|string|max:255|in:FARM,MARKET,SLAUGHTERHOUSE,PASTURE,TRANSPORT',

    //     ];
    //     $communityId = getPermissionsTeamId();
    //     $createdBy = auth()->id();
    //     try {
    //         DB::beginTransaction();
    //         foreach ($request->input('premises', []) as $premiseData) {
    //             $tempRule = $rules;
    //             $existing = Premise::where('uid', $premiseData['uid'] ?? null)->first();
    //             if ($existing) {
    //                 $tempRule['code'] = ['required', 'string', Rule::unique('premises', 'code')->ignore($existing->id)->where('community_id', $communityId)];
    //             } else {
    //                 $tempRule['code'] = ['required', 'string', Rule::unique('premises', 'code')->where('community_id', $communityId)];
    //             }
    //             // Validation basique pour chaque élément
    //             $validator = Validator::make($premiseData, $tempRule);
    //             if ($validator->fails()) {
    //                 $failedValidations[] = [
    //                     'uid' => $premiseData['uid'] ?? null,
    //                     'errors' => $validator->errors()->messages(),
    //                 ];
    //                 continue;
    //             }

    //             $data = $validator->validated();
    //             $data['community_id'] = $communityId;
    //             $data['created_by'] = $createdBy;
    //             Premise::updateOrCreate(
    //                 ['uid' => $premiseData['uid']],
    //                 $data
    //             );
    //         }
    //         DB::commit();
    //     } catch (Exception $e) {
    //         DB::rollback();

    //         return response()->json(
    //             [
    //                 'status' => 'ERROR',
    //                 "errors" => [

    //                 ]
    //             ]
    //             ,
    //             500
    //         );
    //     }

    //     return response()->json([
    //         'status' => 'OK',
    //         'errors' => $failedValidations,
    //     ], 200);
    // }

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
        $user_id = Auth::user()->id;

        $communityId = getPermissionsTeamId();

        foreach ($request->data as $item) {
            DB::beginTransaction();
            try {
                $existing = Premise::where('uid', $item['uid'])->first();

                if (!empty($item['deleted_at'])) {
                    if ($existing) {
                        if ($item['version'] <= $existing->version) {
                            $conflicts[] = [
                                'uid' => $item['uid'],
                                'server_data' => new PremiseResource($existing)
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

                if (!$existing) {
                    $premise = new Premise();
                    $premise->uid = $item['uid'];
                    $data = [
                        'village_id' => $item['village_id'] ?? null,
                        'code' => $item['code'] ?? null,
                        'address' => $item['address'] ?? null,
                        'gps_coordinates' => $item['gps_coordinates'] ?? null,
                        'type' => $item['type'] ?? null,
                        'community_id' => $communityId,
                        'created_by' => $user_id,
                    ];
                    $premise->fill($data);
                    $premise->version = $item['version'];
                    $premise->save();

                    $applied[] = $item['uid'];
                    DB::commit();
                    continue;
                }

                if ($item['version'] <= $existing->version) {
                    $conflicts[] = [
                        'uid' => $item['uid'],
                        'server_data' => new PremiseResource($existing),
                        "local_data" => $item
                    ];
                    DB::rollBack();
                    continue;
                }

                $existing->fill([
                    'village_id' => $item['village_id'] ?? $existing->village_id,
                    'code' => $item['code'] ?? $existing->code,
                    'address' => $item['address'] ?? $existing->address,
                    'gps_coordinates' => $item['gps_coordinates'] ?? $existing->gps_coordinates,
                    'type' => $item['type'] ?? $existing->type,
                ]);
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
}
