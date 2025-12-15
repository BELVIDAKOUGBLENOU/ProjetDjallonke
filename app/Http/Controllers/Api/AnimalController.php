<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnimalRequest;
use App\Http\Resources\AnimalResource;
use App\Models\Animal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AnimalController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        $table = Animal::getTableName();

        return [
            'auth:sanctum',
            new Middleware("permission:list $table", only: ['index', 'getAllData']),
            new Middleware("permission:view $table", only: ['show']),
            new Middleware("permission:create $table", only: ['create', 'store']),
            new Middleware("permission:update $table", only: ['edit', 'update']),
            new Middleware("permission:delete $table", only: ['destroy']),
        ];
    }

    public function getAllData(Request $request): JsonResponse
    {
        $request->validate([
            'community_id' => 'required|exists:communities,id',
        ]);
        $animals = Animal::query()
            ->where('community_id', $request->community_id)
            ->get();

        return response()->json(AnimalResource::collection($animals));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $animals = Animal::paginate();

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
