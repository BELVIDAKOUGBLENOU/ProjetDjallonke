<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\Animal;
use App\Models\MilkRecord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\MilkRecordResource;
use App\Http\Middleware\SetCommunityContextAPI;

class MilkRecordController extends Controller
{
    public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth:sanctum');
        $this->middleware(SetCommunityContextAPI::class);


        // Middleware pour permissions CRUD
        $table = MilkRecord::getTableName();
        // $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show', 'getAllData']);
        $this->middleware("permission:create $table")->only(['create', 'store']);
        $this->middleware("permission:update $table")->only(['edit', 'update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }
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

        $query = MilkRecord::query()
            ->join('events', 'milk_records.event_id', '=', 'events.id')
            ->whereHas('event', function ($qe) use ($communityId) {
                $qe->whereHas('animal', function ($q) use ($communityId) {
                    $q->whereHas('premise', function ($q2) use ($communityId) {
                        $q2->where('community_id', $communityId);
                    });
                });
            })
            ->select('milk_records.*')
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
            'data' => MilkRecordResource::collection($items),
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

                if (!empty($item['deleted_at'])) {
                    if ($existingEvent) {
                        if ($item['version'] <= $existingEvent->version) {
                            $conflicts[] = [
                                'uid' => $item['uid'],
                                'server_data' => new MilkRecordResource($existingEvent->milkRecord)
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
                    MilkRecord::create([
                        'event_id' => $event->id,
                        'milk' => $item['milk'] ?? null,
                    ]);
                    $applied[] = $item['uid'];
                    DB::commit();
                    continue;
                }

                $serverVersion = (int) ($existingEvent->version ?? 0);
                $clientVersion = (int) $item['version'];
                if ($clientVersion <= $serverVersion) {
                    $conflicts[] = ['uid' => $item['uid'], 'server_data' => new MilkRecordResource($existingEvent->milkRecord)];
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

                $mr = $existingEvent->milkRecord ?? new MilkRecord(['event_id' => $existingEvent->id]);
                $mr->milk = $item['milk'] ?? $mr->milk;
                $mr->save();

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

        return response()->json(['status' => 'OK', 'applied' => $applied, 'conflicts' => $conflicts, 'errors' => $errors, 'server_time' => now()->toDateTimeString()]);
    }
}
