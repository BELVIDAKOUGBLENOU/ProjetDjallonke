<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PerformanceRecordRequest;
use App\Http\Resources\PerformanceRecordResource;
use App\Models\PerformanceRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PerformanceRecordController extends Controller
{

    public function getAllData(Request $request): JsonResponse
    {
        $performanceRecords = PerformanceRecord::all();

        return response()->json(PerformanceRecordResource::collection($performanceRecords));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $performanceRecords = PerformanceRecord::paginate();

        return PerformanceRecordResource::collection($performanceRecords);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PerformanceRecordRequest $request): JsonResponse
    {
        $performanceRecord = PerformanceRecord::create($request->validated());

        return response()->json(new PerformanceRecordResource($performanceRecord));
    }

    /**
     * Display the specified resource.
     */
    public function show(PerformanceRecord $performanceRecord): JsonResponse
    {
        return response()->json(new PerformanceRecordResource($performanceRecord));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PerformanceRecordRequest $request, PerformanceRecord $performanceRecord): JsonResponse
    {
        $performanceRecord->update($request->validated());

        return response()->json(new PerformanceRecordResource($performanceRecord));
    }

    /**
     * Delete the specified resource.
     */
    public function destroy(PerformanceRecord $performanceRecord): Response
    {
        $performanceRecord->delete();

        return response()->noContent();
    }
}
