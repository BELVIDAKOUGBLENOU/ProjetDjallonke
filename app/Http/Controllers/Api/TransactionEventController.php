<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use App\Models\Animal;
use App\Models\Person;
use Illuminate\Http\Request;
use App\Models\TransactionEvent;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TransactionEventResource;

class TransactionEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $since = request()->validate([
            'since' => 'nullable|date_format:Y-m-d H:i:s',
        ])['since'] ?? "1970-01-01 00:00:00";
        $query = TransactionEvent::whereHas('event', function ($qe) {
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

        $resource = TransactionEventResource::collection($query);
        $result = $resource->response()->getData(true);
        if ($query->currentPage() >= $query->lastPage()) {
            $result['last_synced_at'] = now()->toDateTimeString();
        }

        return response()->json($result);
    }

    public function push(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
            'data.*.uid' => 'required|string',
            'data.*.version' => 'required|integer',
        ]);

        $applied = [];
        $conflicts = [];
        $errors = [];
        $user_id = Auth::user()->id;


        foreach ($request->input('data', []) as $item) {
            $uid = $item['uid'] ?? null;
            try {
                $validator = Validator::make($item, [
                    'uid' => 'required|string',
                    'version' => 'required|integer',
                    'animal_uid' => 'required|string',
                    'buyer_uid' => 'nullable|string',
                    'seller_uid' => 'nullable|string',
                    'price' => 'nullable|numeric',
                ]);

                if ($validator->fails()) {
                    $errors[] = ['uid' => $uid, 'code' => 'VALIDATION_ERROR', 'message' => $validator->errors()->first()];
                    continue;
                }

                DB::beginTransaction();
                $existingEvent = Event::where('uid', $uid)->first();
                $animal = Animal::where('uid', $item['animal_uid'])->first();
                $buyer = !empty($item['buyer_uid']) ? Person::where('uid', $item['buyer_uid'])->first() : null;
                $seller = !empty($item['seller_uid']) ? Person::where('uid', $item['seller_uid'])->first() : null;
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
                    TransactionEvent::create([
                        'event_id' => $event->id,
                        'buyer_id' => $buyer?->id,
                        'seller_id' => $seller?->id,
                        'price' => $item['price'] ?? null,
                    ]);
                    $applied[] = $uid;
                    DB::commit();
                    continue;
                }

                $serverVersion = (int) ($existingEvent->version ?? 0);
                $clientVersion = (int) $item['version'];
                if ($clientVersion <= $serverVersion) {
                    $conflicts[] = ['uid' => $uid, 'server_data' => (new TransactionEventResource($existingEvent->transactionEvent))->response()->getData(true)];
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

                $trans = $existingEvent->transactionEvent ?? new TransactionEvent(['event_id' => $existingEvent->id]);
                $trans->buyer_id = $buyer?->id ?? $trans->buyer_id;
                $trans->seller_id = $seller?->id ?? $trans->seller_id;
                $trans->price = $item['price'] ?? $trans->price;
                $trans->save();

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
