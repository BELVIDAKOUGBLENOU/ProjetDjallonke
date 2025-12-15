<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WeightRecordRequest;
use App\Http\Resources\WeightRecordResource;
use App\Models\WeightRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class WeightRecordController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        $table = WeightRecord::getTableName();

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
        $weightRecords = WeightRecord::all();

        return response()->json(WeightRecordResource::collection($weightRecords));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $weightRecords = WeightRecord::paginate();

        return WeightRecordResource::collection($weightRecords);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WeightRecordRequest $request): JsonResponse
    {
        $weightRecord = WeightRecord::create($request->validated());

        return response()->json(new WeightRecordResource($weightRecord));
    }

    /**
     * Display the specified resource.
     */
    public function show(WeightRecord $weightRecord): JsonResponse
    {
        return response()->json(new WeightRecordResource($weightRecord));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WeightRecordRequest $request, WeightRecord $weightRecord): JsonResponse
    {
        $weightRecord->update($request->validated());

        return response()->json(new WeightRecordResource($weightRecord));
    }

    /**
     * Delete the specified resource.
     */
    public function destroy(WeightRecord $weightRecord): Response
    {
        $weightRecord->delete();

        return response()->noContent();
    }
}
