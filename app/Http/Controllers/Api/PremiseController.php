<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Premise;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Requests\PremiseRequest;
use App\Http\Resources\PremiseResource;
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
    public function index(Request $request)
    {
        $premises = Premise::
            where("community_id", getPermissionsTeamId());

        $since = $request->validate([
            'since' => 'nullable|date_format:Y-m-d H:i:s',
        ])['since'] ?? "1970-01-01 00:00:00";
        if ($since) {
            $premises = $premises->where('updated_at', '>=', $since);
        }

        $premises = $premises->paginate();
        $resource = PremiseResource::collection($premises);
        $result = $resource->response()->getData(true);
        // si on est à la derniere page , on ajoute les last_synced_at
        if ($premises->currentPage() >= $premises->lastPage()) {
            $result['last_synced_at'] = now()->toDateTimeString();
        }

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PremiseRequest $request): JsonResponse
    {
        $all = $request->validated();
        $all['community_id'] = getPermissionsTeamId();
        $all['created_by'] = auth()->id();
        $premise = Premise::create($all);

        return response()->json(new PremiseResource($premise));
    }

    /**
     * Display the specified resource.
     */
    public function show(Premise $premise): JsonResponse
    {
        return response()->json(new PremiseResource($premise));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PremiseRequest $request, Premise $premise): JsonResponse
    {
        $premise->update($request->validated());

        return response()->json(new PremiseResource($premise));
    }

    /**
     * Delete the specified resource.
     */
    public function destroy(Premise $premise): Response
    {
        $premise->delete();

        return response()->noContent();
    }

    public function syncPremises(Request $request): JsonResponse
    {
        $this->middleware(['auth:sanctum', SetCommunityContextAPI::class, "can:create " . Premise::getTableName()]);
        $v1 = Validator::make($request->all(), [
            'premises' => 'required|array',
            "premises.*.uid" => 'required|string',
        ]);
        if ($v1->fails()) {
            return response()->json([
                'status' => 'ERROR',
                'errors' => $v1->errors()->messages(),
            ], 400);
        }
        $failedValidations = [];
        $rules = [

            'village_id' => 'required|integer|exists:villages,id',
            'code' => 'required|string|unique:premises,code',
            'address' => 'required|string|max:255',
            'gps_coordinates' => 'required|string|max:255',
            'health_status' => 'nullable|string|max:255',
            'type' => 'required|string|max:255|in:FARM,MARKET,SLAUGHTERHOUSE,PASTURE,TRANSPORT',

        ];
        $communityId = getPermissionsTeamId();
        $createdBy = auth()->id();
        try {
            DB::beginTransaction();
            foreach ($request->input('premises', []) as $premiseData) {
                $tempRule = $rules;
                $existing = Premise::where('uid', $premiseData['uid'] ?? null)->first();
                if ($existing) {
                    $tempRule['code'] = ['required', 'string', Rule::unique('premises', 'code')->ignore($existing->id)->where('community_id', $communityId)];
                } else {
                    $tempRule['code'] = ['required', 'string', Rule::unique('premises', 'code')->where('community_id', $communityId)];
                }
                // Validation basique pour chaque élément
                $validator = Validator::make($premiseData, $tempRule);
                if ($validator->fails()) {
                    $failedValidations[] = [
                        'uid' => $premiseData['uid'] ?? null,
                        'errors' => $validator->errors()->messages(),
                    ];
                    continue;
                }

                $data = $validator->validated();
                $data['community_id'] = $communityId;
                $data['created_by'] = $createdBy;
                Premise::updateOrCreate(
                    ['uid' => $premiseData['uid']],
                    $data
                );
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(
                [
                    'status' => 'ERROR',
                    "errors" => [

                    ]
                ]
                ,
                500
            );
        }

        return response()->json([
            'status' => 'OK',
            'errors' => $failedValidations,
        ], 200);
    }
}
