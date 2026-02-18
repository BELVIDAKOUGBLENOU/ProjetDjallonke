<?php

namespace App\Http\Controllers\Api\Syncing;

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

class CommunitySyncController extends Controller
{

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
    public function pull(Request $request)
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

}
