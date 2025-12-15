<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VillageRequest;
use App\Http\Resources\VillageResource;
use App\Models\Village;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class VillageController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        $table = Village::getTableName();

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
        $villages = Village::all();

        return response()->json(VillageResource::collection($villages));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $subDistrict = $request->sub_district_id ?? null;
        $villages = Village::query()
            ->when($subDistrict, function ($query, $subDistrict) {
                $query->where('sub_district_id', $subDistrict);
            })->paginate();

        return VillageResource::collection($villages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VillageRequest $request): JsonResponse
    {
        $village = Village::create($request->validated());

        return response()->json(new VillageResource($village));
    }

    /**
     * Display the specified resource.
     */
    public function show(Village $village): JsonResponse
    {
        return response()->json(new VillageResource($village));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VillageRequest $request, Village $village): JsonResponse
    {
        $village->update($request->validated());

        return response()->json(new VillageResource($village));
    }

    /**
     * Delete the specified resource.
     */
    public function destroy(Village $village): Response
    {
        $village->delete();

        return response()->noContent();
    }
}
