<?php

namespace App\Http\Controllers\Api;

use App\Models\ReproductionEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReproductionEventResource;

class ReproductionEventController extends Controller
{
    public function index()
    {
        $since = request()->validate([
            'since' => 'nullable|date_format:Y-m-d H:i:s',
        ])['since'] ?? "1970-01-01 00:00:00";
        $query = ReproductionEvent::whereHas('event', function ($qe) {
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

        $resource = ReproductionEventResource::collection($query);
        $result = $resource->response()->getData(true);
        if ($query->currentPage() >= $query->lastPage()) {
            $result['last_synced_at'] = now()->toDateTimeString();
        }

        return response()->json($result);
    }
}
