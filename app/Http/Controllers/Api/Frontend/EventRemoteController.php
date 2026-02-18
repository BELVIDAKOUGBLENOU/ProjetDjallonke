<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetCommunityContextFrontend;
use App\Http\Requests\EventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventRemoteController extends Controller
{
    public function __construct()
    {
        $table = Event::getTableName();
        $this->middleware(SetCommunityContextFrontend::class);
        $this->middleware("permission:list events")->only('index');
        $this->middleware("permission:update events")->only(['update']);
        $this->middleware("permission:delete events")->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $limit = $request->integer('limit', 10);
        $status = $request->string('status')->toString();
        $type = $request->string('type')->toString();

        $communityId = getPermissionsTeamId(); // Or context

        $query = Event::query();

        if ($communityId) {
            $query->whereHas('animal.premise', function ($q) use ($communityId) {
                $q->where('community_id', $communityId);
            });
        }

        if ($q) {
            // Simple search on comment or animal UID
            $query->where(function ($sub) use ($q) {
                $sub->where('comment', 'like', "%{$q}%")
                    ->orWhereHas('animal', function ($sq) use ($q) {
                        $sq->where('uid', 'like', "%{$q}%");
                    });
            });
        }

        if ($status === 'pending') {
            $query->where('is_confirmed', false);
        } elseif ($status === 'confirmed') {
            $query->where('is_confirmed', true);
        }

        if ($type) {
            switch ($type) {
                case 'health_event':
                    $query->has('healthEvent');
                    break;
                case 'movement_event':
                    $query->has('movementEvent');
                    break;
                case 'transaction_event':
                    $query->has('transactionEvent');
                    break;
                case 'reproduction_event':
                    $query->has('reproductionEvent');
                    break;
                case 'birth_event':
                    $query->has('birthEvent');
                    break;
                case 'milk_record':
                    $query->has('milkRecord');
                    break;
                case 'weight_record':
                    $query->has('weightRecord');
                    break;
                case 'death_event':
                    $query->has('deathEvent');
                    break;
            }
        }

        // Eager load all subtypes + animal with its premise + creator
        $events = $query->with([
            'creator',
            'animal.premise',
            'healthEvent',
            'movementEvent',
            'transactionEvent',
            'reproductionEvent',
            'birthEvent',
            'milkRecord',
            'deathEvent',
            'weightRecord'
        ])
            ->latest('event_date')
            ->paginate($limit);

        return EventResource::collection($events);
    }

    /**
     * Get statistics for filtering.
     */
    public function statistics(Request $request)
    {
        $status = $request->string('status')->toString();
        $communityId = getPermissionsTeamId();

        $baseQuery = Event::query();
        if ($communityId) {
            $baseQuery->whereHas('animal.premise', function ($q) use ($communityId) {
                $q->where('community_id', $communityId);
            });
        }

        // If user selects a status (e.g. pending), the counts should reflect that subset?
        // Usually dashboards show "Total Pending", "Pending Health", etc. OR "Total Health" regardless of status.
        // Given the UI is "Filters", usually changing one filter updates the counts of the others IF they are drill-down.
        // But here these are parallel categories. Let's make them reflect the current global status filter if set.
        if ($status === 'pending') {
            $baseQuery->where('is_confirmed', false);
        } elseif ($status === 'confirmed') {
            $baseQuery->where('is_confirmed', true);
        }

        return response()->json([
            'total' => (clone $baseQuery)->count(),
            'health_event' => (clone $baseQuery)->has('healthEvent')->count(),
            'movement_event' => (clone $baseQuery)->has('movementEvent')->count(),
            'transaction_event' => (clone $baseQuery)->has('transactionEvent')->count(),
            'reproduction_event' => (clone $baseQuery)->has('reproductionEvent')->count(),
            'birth_event' => (clone $baseQuery)->has('birthEvent')->count(),
            'milk_record' => (clone $baseQuery)->has('milkRecord')->count(),
            'weight_record' => (clone $baseQuery)->has('weightRecord')->count(),
            'death_event' => (clone $baseQuery)->has('deathEvent')->count(),
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::with([
            'creator',
            'animal.premise',
            'healthEvent',
            'movementEvent',
            'transactionEvent',
            'reproductionEvent',
            'birthEvent',
            'milkRecord',
            'deathEvent',
            'weightRecord'
        ])->findOrFail($id);

        return new EventResource($event);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventRequest $request, string $id)
    {
        $event = Event::findOrFail($id);

        $data = $request->validated();

        // If confirming, set confirmed_by
        if (isset($data['is_confirmed']) && $data['is_confirmed'] && !$event->is_confirmed) {
            $data['confirmed_by'] = Auth::id();
        }

        $event->update($data);

        return new EventResource($event->load(['creator', 'animal']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(null, 204);
    }
}
