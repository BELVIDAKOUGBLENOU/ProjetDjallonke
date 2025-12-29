<?php

namespace App\Http\Controllers\Api;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonRequest;
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
        $this->middleware("permission:view $table")->only(['show', 'getAllData']);
        $this->middleware("permission:create $table")->only(['create', 'store']);
        $this->middleware("permission:update $table")->only(['edit', 'update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }

    public function getAllData(Request $request): JsonResponse
    {
        $communityId = getPermissionsTeamId();

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
            ->get();

        return response()->json(PersonResource::collection($persons));
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(PersonRequest $request): JsonResponse
    {
        $person = Person::create($request->validated());

        return response()->json(new PersonResource($person));
    }

    /**
     * Display the specified resource.
     */
    public function show(Person $person): JsonResponse
    {
        return response()->json(new PersonResource($person));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PersonRequest $request, Person $person): JsonResponse
    {
        $person->update($request->validated());

        return response()->json(new PersonResource($person));
    }

    /**
     * Delete the specified resource.
     */
    public function destroy(Person $person): Response
    {
        $person->delete();

        return response()->noContent();
    }
}
