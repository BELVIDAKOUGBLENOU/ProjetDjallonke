<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PremiseRequest;
use App\Http\Resources\PremiseResource;
use App\Models\Premise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PremiseController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        $table = Premise::getTableName();

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
        $premises = Premise::all();

        return response()->json(PremiseResource::collection($premises));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $premises = Premise::paginate();

        return PremiseResource::collection($premises);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PremiseRequest $request): JsonResponse
    {
        $premise = Premise::create($request->validated());

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
