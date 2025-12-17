<?php

namespace App\Http\Controllers\Api;

use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\DistrictRequest;
use App\Http\Resources\DistrictResource;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Middleware\SetCommunityContextAPI;
use Illuminate\Routing\Controllers\HasMiddleware;

class DistrictController extends Controller
{
    public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth');
        $this->middleware(SetCommunityContextAPI::class);

        // Middleware pour permissions CRUD
        $table = District::getTableName();
        $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show']);
        $this->middleware("permission:create $table")->only(['create', 'store']);
        $this->middleware("permission:update $table")->only(['edit', 'update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }

    public function getAllData(Request $request): JsonResponse
    {
        $country = $request->country_id ?? null;
        $districts = District::query()
            ->when($country, function ($query, $country) {
                $query->where('country_id', $country);
            });

        $imbriqued = $request->boolean('imbriqued');
        if ($imbriqued) {
            $districts->with('subDistricts.villages');
        }
        $districts = $districts->get();

        $resource = DistrictResource::collection($districts);
        $resource->each(fn($r) => $r->setImbriqued($imbriqued));

        return response()->json($resource);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $country = $request->country_id ?? null;
        $imbriqued = $request->boolean('imbriqued');

        $districts = District::query()
            ->when($country, function ($query, $country) {
                $query->where('country_id', $country);
            });

        if ($imbriqued) {
            $districts->with('subDistricts.villages');
        }

        $districts = $districts->paginate();

        $resource = DistrictResource::collection($districts);
        $resource->each(fn($r) => $r->setImbriqued($imbriqued));

        return $resource;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DistrictRequest $request): JsonResponse
    {
        $district = District::create($request->validated());

        return response()->json(new DistrictResource($district));
    }

    /**
     * Display the specified resource.
     */
    public function show(District $district): JsonResponse
    {
        return response()->json(new DistrictResource($district));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DistrictRequest $request, District $district): JsonResponse
    {
        $district->update($request->validated());

        return response()->json(new DistrictResource($district));
    }

    /**
     * Delete the specified resource.
     */
    public function destroy(District $district): Response
    {
        $district->delete();

        return response()->noContent();
    }
}
