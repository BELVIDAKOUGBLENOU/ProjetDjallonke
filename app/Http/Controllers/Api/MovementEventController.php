<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\Animal;
use App\Models\Premise;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\MovementEvent;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\MovementEventResource;

class MovementEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $communityId = getPermissionsTeamId();

        $validated = $request->validate([
            'cursor.updated_at' => 'nullable|date_format:Y-m-d H:i:s',
            'cursor.uid' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:200'
        ]);

        $limit = $validated['limit'] ?? 100;
        $cursorUpdatedAt = $validated['cursor']['updated_at'] ?? null;
        $cursorUid = $validated['cursor']['uid'] ?? null;

        $query = MovementEvent::query()
            ->join('events', 'movement_events.event_id', '=', 'events.id')
            ->whereHas('event', function ($qe) use ($communityId) {
                $qe->whereHas('animal', function ($q) use ($communityId) {
                    $q->whereHas('premise', function ($q2) use ($communityId) {
                        $q2->where('community_id', $communityId);
                    });
                });
            })
            ->select('movement_events.*')
            ->orderBy('events.updated_at')
            ->orderBy('events.uid');

        if ($cursorUpdatedAt && $cursorUid) {
            $query->where(function ($q) use ($cursorUpdatedAt, $cursorUid) {
                $q->where('events.updated_at', '>', $cursorUpdatedAt)
                    ->orWhere(function ($q2) use ($cursorUpdatedAt, $cursorUid) {
                        $q2->where('events.updated_at', $cursorUpdatedAt)
                            ->where('events.uid', '>', $cursorUid);
                    });
            });
        }

        $items = $query->with('event')->limit($limit + 1)->get();

        $hasMore = $items->count() > $limit;
        $items = $items->take($limit);

        $nextCursor = null;
        if ($items->isNotEmpty()) {
            $last = $items->last();
            $nextCursor = [
                'updated_at' => $last->event->updated_at->toDateTimeString(),
                'uid' => $last->event->uid
            ];
        }

        return response()->json([
            'data' => MovementEventResource::collection($items),
            'cursor' => $nextCursor,
            'has_more' => $hasMore,
            'server_time' => now()->toDateTimeString()
        ]);

    }

    public function push(Request $request): JsonResponse
    {
        $request->validate([
            'data' => 'required|array',
            'data.*.uid' => 'required|string',
            'data.*.version' => 'required|integer',
            'data.*.deleted_at' => 'nullable|date'
        ]);
        $user_id = Auth::user()->id;

        $applied = [];
        $conflicts = [];
        $errors = [];

        foreach ($request->data as $item) {
            DB::beginTransaction();
            try {
                $existingEvent = Event::where('uid', $item['uid'])->first();
                $animal = Animal::where('uid', $item['animal_uid'] ?? null)->first();
                if (!$animal) {
                    DB::rollBack();
                    $errors[] = ['uid' => $item['uid'] ?? null, 'code' => 'MISSING_RELATION', 'message' => 'Animal not found'];
                    continue;
                }

                $from = !empty($item['from_premise_uid']) ? Premise::where('uid', $item['from_premise_uid'])->first() : null;
                $to = !empty($item['to_premise_uid']) ? Premise::where('uid', $item['to_premise_uid'])->first() : null;

                if (!empty($item['deleted_at'])) {
                    if ($existingEvent) {
                        if ($item['version'] <= $existingEvent->version) {
                            $conflicts[] = [
                                'uid' => $item['uid'],
                                'server_data' => new MovementEventResource($existingEvent->movementEvent)
                            ];
                            DB::rollBack();
                            continue;
                        }

                        $existingEvent->deleted_at = $item['deleted_at'];
                        $existingEvent->version = $item['version'];
                        $existingEvent->save();
                    }

                    $applied[] = $item['uid'];
                    DB::commit();
                    continue;
                }

                if (!$existingEvent) {
                    $event = Event::create([
                        'uid' => $item['uid'],
                        'version' => $item['version'],
                        'animal_id' => $animal->id,
                        'source' => $item['source'] ?? null,
                        'event_date' => $item['event_date'] ?? null,
                        'comment' => $item['comment'] ?? null,
                        'created_by' => $user_id,
                    ]);
                    MovementEvent::create([
                        'event_id' => $event->id,
                        'from_premises_id' => $from?->id,
                        'to_premises_id' => $to?->id,
                        'change_owner' => $item['change_owner'] ?? false,
                        'change_keeper' => $item['change_keeper'] ?? false,
                    ]);
                    $applied[] = $item['uid'];
                    DB::commit();
                    continue;
                }

                $serverVersion = (int) ($existingEvent->version ?? 0);
                $clientVersion = (int) $item['version'];
                if ($clientVersion <= $serverVersion) {
                    $conflicts[] = ['uid' => $item['uid'], 'server_data' => new MovementEventResource($existingEvent->movementEvent)];
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

                $move = $existingEvent->movementEvent ?? new MovementEvent(['event_id' => $existingEvent->id]);
                $move->from_premises_id = $from?->id ?? $move->from_premises_id;
                $move->to_premises_id = $to?->id ?? $move->to_premises_id;
                $move->change_owner = $item['change_owner'] ?? $move->change_owner;
                $move->change_keeper = $item['change_keeper'] ?? $move->change_keeper;
                $move->save();

                $applied[] = $item['uid'];
                DB::commit();

            } catch (QueryException $qe) {
                DB::rollBack();
                $errors[] = ['uid' => $item['uid'] ?? null, 'code' => 'UNIQUE_CONSTRAINT', 'message' => $qe->getMessage()];
            } catch (\Throwable $e) {
                DB::rollBack();
                $errors[] = ['uid' => $item['uid'] ?? null, 'code' => 'SERVER_ERROR', 'message' => $e->getMessage()];
            }
        }

        return response()->json([
            'status' => 'OK',
            'applied' => $applied,
            'conflicts' => $conflicts,
            'errors' => $errors,
            'server_time' => now()->toDateTimeString()
        ]);
    }
}
