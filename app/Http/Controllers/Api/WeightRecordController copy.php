<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\Animal;
use App\Models\WeightRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\WeightRecordRequest;
use App\Http\Resources\WeightRecordResource;

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

    public function push(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
            'data.*.uid' => 'required|string',
            'data.*.version' => 'required|integer',
        ]);
        $user_id = Auth::user()->id;

        $applied = [];
        $conflicts = [];
        $errors = [];

        foreach ($request->input('data', []) as $item) {
            $uid = $item['uid'] ?? null;
            try {
                $validator = Validator::make($item, [
                    'uid' => 'required|string',
                    'version' => 'required|integer',
                    'animal_uid' => 'required|string',
                    'weight' => 'nullable|numeric',
                ]);

                if ($validator->fails()) {
                    $errors[] = ['uid' => $uid, 'code' => 'VALIDATION_ERROR', 'message' => $validator->errors()->first()];
                    continue;
                }

                DB::beginTransaction();
                $existingEvent = Event::where('uid', $uid)->first();
                $animal = Animal::where('uid', $item['animal_uid'])->first();
                if (!$animal) {
                    DB::rollBack();
                    $errors[] = ['uid' => $uid, 'code' => 'MISSING_RELATION', 'message' => 'Animal not found'];
                    continue;
                }

                if (!$existingEvent) {
                    $event = Event::create([
                        'uid' => $uid,
                        'version' => $item['version'],
                        'animal_id' => $animal->id,
                        'source' => $item['source'] ?? null,
                        'event_date' => $item['event_date'] ?? null,
                        'comment' => $item['comment'] ?? null,
                        'created_by' => $user_id,
                    ]);
                    WeightRecord::create([
                        'event_id' => $event->id,
                        'weight' => $item['weight'] ?? null,
                    ]);
                    $applied[] = $uid;
                    DB::commit();
                    continue;
                }

                $serverVersion = (int) ($existingEvent->version ?? 0);
                $clientVersion = (int) $item['version'];
                if ($clientVersion <= $serverVersion) {
                    $conflicts[] = ['uid' => $uid, 'server_data' => (new WeightRecordResource($existingEvent->weightRecord))->response()->getData(true)];
                    DB::rollBack();
                    continue;
                }

                $existingEvent->fill([
                    'version' => $clientVersion,
                    'animal_id' => $animal->id,
                    'source' => $item['source'] ?? $existingEvent->source,
                    'event_date' => $item['event_date'] ?? $existingEvent->event_date,
                    'comment' => $item['comment'] ?? $existingEvent->comment,
                ]);
                $existingEvent->save();

                $wr = $existingEvent->weightRecord ?? new WeightRecord(['event_id' => $existingEvent->id]);
                $wr->weight = $item['weight'] ?? $wr->weight;
                $wr->save();

                $applied[] = $uid;
                DB::commit();

            } catch (QueryException $qe) {
                DB::rollBack();
                $errors[] = ['uid' => $uid, 'code' => 'UNIQUE_CONSTRAINT', 'message' => $qe->getMessage()];
            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = ['uid' => $uid, 'code' => 'UNKNOWN_ERROR', 'message' => $e->getMessage()];
            }
        }

        return response()->json(['statut' => 'OK', 'applied' => $applied, 'conflicts' => $conflicts, 'errors' => $errors]);
    }
}
