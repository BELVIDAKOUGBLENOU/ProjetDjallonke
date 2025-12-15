<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRequest;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CountryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        $table = Country::getTableName();

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
        $countries = Country::query();
        $imbriqued = $request->boolean('imbriqued');
        if ($imbriqued) {
            $countries = $countries->with('districts');
        }
        $countries = $countries->get();

        $resource = CountryResource::collection($countries);
        $resource->each(fn ($r) => $r->setImbriqued($imbriqued));

        return response()->json($resource);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $countries = Country::query();
        $imbriqued = $request->boolean('imbriqued');
        if ($imbriqued) {
            $countries = $countries->with('districts');
        }
        $countries = $countries->paginate();

        $resource = CountryResource::collection($countries);
        $resource->each(fn ($r) => $r->setImbriqued($imbriqued));

        return $resource;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CountryRequest $request): JsonResponse
    {
        $country = Country::create($request->validated());

        return response()->json(new CountryResource($country));
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country): JsonResponse
    {
        return response()->json(new CountryResource($country));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CountryRequest $request, Country $country): JsonResponse
    {
        $country->update($request->validated());

        return response()->json(new CountryResource($country));
    }

    /**
     * Delete the specified resource.
     */
    public function destroy(Country $country): Response
    {
        try {
            $country->delete();

            return response()->noContent();
        } catch (\Exception $e) {
            return response('Impossible de supprimer ce pays', 500);
        }

    }
}
