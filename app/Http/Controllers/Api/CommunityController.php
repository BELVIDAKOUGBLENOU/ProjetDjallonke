<?php

namespace App\Http\Controllers\Api;

use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommunityRequest;
use App\Http\Resources\CommunityResource;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Middleware\SetCommunityContextAPI;
use Illuminate\Routing\Controllers\HasMiddleware;

class CommunityController extends Controller
{
    // public static function middleware(): array
    // {

    //     return [
    //         'auth:sanctum',
    //         new Middleware("permission:list $table", only: ['index', 'getAllData']),
    //         new Middleware("permission:view $table", only: ['show']),
    //         new Middleware("permission:create $table", only: ['create', 'store']),
    //         new Middleware("permission:update $table", only: ['edit', 'update']),
    //         new Middleware("permission:delete $table", only: ['destroy']),
    //     ];
    // }
    public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth:sanctum');
        $this->middleware(SetCommunityContextAPI::class);
        $this->middleware(function ($request, $next) {

            $communityId = $request->route('api_community');

            if ($communityId) {
                setPermissionsTeamId($communityId);
            }


            return $next($request);
        });


        // Middleware pour permissions CRUD
        $table = Community::getTableName();
        // $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show']);
        $this->middleware("permission:create $table")->only(['create', 'store']);
        $this->middleware("permission:update $table")->only(['edit', 'update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $since = $request->validate([
            'since' => 'nullable|date_format:Y-m-d H:i:s',
        ])['since'] ?? "1970-01-01 00:00:00";
        $communities = Community::
            where(function ($query) {
                $query->whereHas('members', function ($query) {
                    $query->where('users.id', auth()->id());
                });
            })->
            when($since, function ($query, $since) {
                $query->where(function ($q) use ($since) {
                    $q->where('created_at', '>=', $since)
                        ->orWhere('updated_at', '>=', $since);
                });
            })
            ->paginate();
        $resource = CommunityResource::collection($communities);
        $result = $resource->response()->getData(true);
        if ($communities->currentPage() >= $communities->lastPage()) {
            $result['last_synced_at'] = now()->toDateTimeString();
        }
        return $result;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommunityRequest $request): JsonResponse
    {
        $community = Community::create($request->validated());

        return response()->json(new CommunityResource($community));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $communityId): JsonResponse
    {
        $community = Community::findOrFail($communityId);
        $community->load(['country', 'creator']);
        // dd($community->toArray());
        return response()->json(new CommunityResource($community));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommunityRequest $request, Community $community): JsonResponse
    {
        $community->update($request->validated());

        return response()->json(new CommunityResource($community));
    }

    /**
     * Delete the specified resource.
     */
    public function destroy(Community $community): Response
    {
        $community->delete();

        return response()->noContent();
    }
}
