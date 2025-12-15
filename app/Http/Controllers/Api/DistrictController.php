<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DistrictRequest;
use App\Http\Resources\DistrictResource;
use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DistrictController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        $table = District::getTableName();

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
        $districts = District::query();
        $imbriqued = $request->boolean('imbriqued');
        if ($imbriqued) {
            $districts->with('subDistricts');
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
            $districts->with('subDistricts');
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
