<?php

namespace App\Http\Controllers\Api\Syncing;

use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\VillageRequest;
use App\Http\Resources\VillageResource;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Middleware\SetCommunityContextAPI;
use Illuminate\Routing\Controllers\HasMiddleware;

class VillageSyncController extends Controller
{
    public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth');
        $this->middleware(SetCommunityContextAPI::class);

        // Middleware pour permissions CRUD
        $table = Village::getTableName();
        $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show']);
        $this->middleware("permission:create $table")->only(['create', 'store']);
        $this->middleware("permission:update $table")->only(['edit', 'update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }

    public function getAllData(Request $request): JsonResponse
    {
        $villages = Village::all();

        return response()->json(VillageResource::collection($villages));
    }

    /**
     * Display a listing of the resource.
     */
    public function pull(Request $request)
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
