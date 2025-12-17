<?php

namespace App\Http\Controllers\Api;

use App\Models\Premise;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PremiseRequest;
use App\Http\Resources\PremiseResource;
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
            where("community_id", getPermissionsTeamId())->
            paginate();

        return PremiseResource::collection($premises);
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
}
