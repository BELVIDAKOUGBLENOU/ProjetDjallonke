<?php

namespace App\Http\Controllers\Api;

use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnimalRequest;
use App\Http\Resources\AnimalResource;
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

    public function getAllData(Request $request): JsonResponse
    {
        $communityId = getPermissionsTeamId();
        $animals = Animal::query()
            ->where('community_id', $communityId)
            ->get();

        return response()->json(AnimalResource::collection($animals));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $communityId = getPermissionsTeamId();

        $animals = Animal::
            where('community_id', $communityId)->
            paginate();

        return AnimalResource::collection($animals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnimalRequest $request): JsonResponse
    {
        $animal = Animal::create($request->validated());

        return response()->json(new AnimalResource($animal));
    }

    /**
     * Display the specified resource.
     */
    public function show(Animal $animal): JsonResponse
    {
        return response()->json(new AnimalResource($animal));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnimalRequest $request, Animal $animal): JsonResponse
    {
        $animal->update($request->validated());

        return response()->json(new AnimalResource($animal));
    }

    /**
     * Delete the specified resource.
     */
    public function destroy(Animal $animal): Response
    {
        $animal->delete();

        return response()->noContent();
    }
}
