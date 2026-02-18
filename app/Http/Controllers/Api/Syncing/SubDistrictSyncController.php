<?php

namespace App\Http\Controllers\Api\Syncing;

use App\Models\SubDistrict;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubDistrictRequest;
use App\Http\Resources\SubDistrictResource;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Middleware\SetCommunityContextAPI;
use Illuminate\Routing\Controllers\HasMiddleware;

class SubDistrictSyncController extends Controller
{
    public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth');
        $this->middleware(SetCommunityContextAPI::class);

        // Middleware pour permissions CRUD
        $table = SubDistrict::getTableName();
        $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show']);
        $this->middleware("permission:create $table")->only(['create', 'store']);
        $this->middleware("permission:update $table")->only(['edit', 'update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }

    public function getAllData(Request $request): JsonResponse
    {
        $subDistricts = SubDistrict::query();
        $imbriqued = $request->boolean('imbriqued');
        if ($imbriqued) {
            $subDistricts->with('villages');
        }
        $subDistricts = $subDistricts->get();

        $resource = SubDistrictResource::collection($subDistricts);
        $resource->each(fn($r) => $r->setImbriqued($imbriqued));

        return response()->json($resource);
    }

    /**
     * Display a listing of the resource.
     */
    public function pull(Request $request)
    {
        $district = $request->district_id ?? null;
        $imbriqued = $request->boolean('imbriqued');

        $subDistricts = SubDistrict::query()
            ->when($district, function ($query, $district) {
                $query->where('district_id', $district);
            });

        if ($imbriqued) {
            $subDistricts->with('villages');
        }

        $subDistricts = $subDistricts->paginate();

        $resource = SubDistrictResource::collection($subDistricts);
        $resource->each(fn($r) => $r->setImbriqued($imbriqued));

        return $resource;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubDistrictRequest $request): JsonResponse
    {
        $subDistrict = SubDistrict::create($request->validated());

        return response()->json(new SubDistrictResource($subDistrict));
    }

    /**
     * Display the specified resource.
     */
    public function show(SubDistrict $subDistrict): JsonResponse
    {
        return response()->json(new SubDistrictResource($subDistrict));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubDistrictRequest $request, SubDistrict $subDistrict): JsonResponse
    {
        $subDistrict->update($request->validated());

        return response()->json(new SubDistrictResource($subDistrict));
    }

    /**
     * Delete the specified resource.
     */
    public function destroy(SubDistrict $subDistrict): Response
    {
        $subDistrict->delete();

        return response()->noContent();
    }
}
