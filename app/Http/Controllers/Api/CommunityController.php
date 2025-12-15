<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommunityRequest;
use App\Http\Resources\CommunityResource;
use App\Models\Community;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CommunityController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        $table = Community::getTableName();

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
        $communities = Community::all();

        return response()->json(CommunityResource::collection($communities));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $communities = Community::paginate();

        return CommunityResource::collection($communities);
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
    public function show(Community $community): JsonResponse
    {
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
