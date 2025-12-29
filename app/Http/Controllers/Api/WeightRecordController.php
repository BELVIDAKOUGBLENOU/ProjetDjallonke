<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WeightRecordRequest;
use App\Http\Resources\WeightRecordResource;
use App\Models\WeightRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WeightRecordController extends Controller
{
    public function getAllData(Request $request): JsonResponse
    {
        $weightRecords = WeightRecord::all();

        return response()->json(WeightRecordResource::collection($weightRecords));
    }

    /**
     * Display a listing of the resource (pull endpoint filtered by community).
     */
    public function index(Request $request)
    {
        $since = $request->validate([
            'since' => 'nullable|date_format:Y-m-d H:i:s',
        ])['since'] ?? "1970-01-01 00:00:00";

        $query = WeightRecord::whereHas('event', function ($qe) {
            $communityId = getPermissionsTeamId();
            $qe->whereHas('animal', function ($q) use ($communityId) {
                $q->whereHas('premise', function ($q2) use ($communityId) {
                    $q2->where('community_id', $communityId);
                });
            });
        })->when($since, function ($query, $since) {
            $query->where(function ($q) use ($since) {
                $q->where('created_at', '>=', $since)
                    ->orWhere('updated_at', '>=', $since);
            });
        })->with('event')->paginate();

        $resource = WeightRecordResource::collection($query);
        $result = $resource->response()->getData(true);
        if ($query->currentPage() >= $query->lastPage()) {
            $result['last_synced_at'] = now()->toDateTimeString();
        }

        return response()->json($result);
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
