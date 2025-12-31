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
        $validated = $request->validate([
            'cursor.updated_at' => 'nullable|date_format:Y-m-d H:i:s',
            'cursor.uid' => 'nullable',
            'limit' => 'nullable|integer|min:1|max:200',
        ]);

        $limit = $validated['limit'] ?? 100;
        $cursorUpdatedAt = $validated['cursor']['updated_at'] ?? null;
        $cursorUid = $validated['cursor']['uid'] ?? null;
        $cursorId = $cursorUid !== null ? (int) $cursorUid : null;

        $query = Community::query()
            ->where(function ($query) {
                $query->whereHas('members', function ($query) {
                    $query->where('users.id', auth()->id());
                });
            })
            ->orderBy('updated_at')
            ->orderBy('id');

        if ($cursorUpdatedAt && $cursorId) {
            $query->where(function ($q) use ($cursorUpdatedAt, $cursorId) {
                $q->where('updated_at', '>', $cursorUpdatedAt)
                    ->orWhere(function ($q2) use ($cursorUpdatedAt, $cursorId) {
                        $q2->where('updated_at', $cursorUpdatedAt)
                            ->where('id', '>', $cursorId);
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
                'uid' => (string) $last->id,
            ];
        }

        $resource = CommunityResource::collection($items);

        return response()->json([
            'data' => $resource,
            'cursor' => $nextCursor,
            'has_more' => $hasMore,
            'server_time' => now()->toDateTimeString(),
        ]);
    }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(CommunityRequest $request): JsonResponse
    // {
    //     $community = Community::create($request->validated());

    //     return response()->json(new CommunityResource($community));
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(string $communityId): JsonResponse
    // {
    //     $community = Community::findOrFail($communityId);
    //     $community->load(['country', 'creator']);
    //     // dd($community->toArray());
    //     return response()->json(new CommunityResource($community));
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(CommunityRequest $request, Community $community): JsonResponse
    // {
    //     $community->update($request->validated());

    //     return response()->json(new CommunityResource($community));
    // }

    // /**
    //  * Delete the specified resource.
    //  */
    // public function destroy(Community $community): Response
    // {
    //     $community->delete();

    //     return response()->noContent();
    // }
}
