<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PersonController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        $table = Person::getTableName();

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
        $comminityId = $request->validate([
            'community_id' => 'required|exists:communities,id',
        ])['community_id'] ?? null;
        $persons = Person::query()
            ->when($comminityId, function ($query, $comminityId) {
                $query->where('community_id', $comminityId);
            })->get()
        ;

        return response()->json(PersonResource::collection($persons));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $comminityId = $request->validate([
            'community_id' => 'required|exists:communities,id',
        ])['community_id'] ?? null;
        $persons = Person::where('community_id', $comminityId)->paginate();

        return PersonResource::collection($persons);
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
