<?php

namespace App\Http\Controllers\Api;

use App\Models\PersonRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PersonRoleResource;

class PersonRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $communityId = getPermissionsTeamId();
        $since = $request->validate([
            'since' => 'nullable|date_format:Y-m-d H:i:s',
        ])['since'] ?? "1970-01-01 00:00:00";

        $roles = PersonRole::query()
            ->when($communityId, function ($query) use ($communityId) {
                $query->whereHas('animal', function ($q) use ($communityId) {
                    $q->whereHas('premise', function ($q) use ($communityId) {
                        $q->where('community_id', $communityId);
                    });
                });
            })
            ->when($since, function ($query) use ($since) {
                $query->where(function ($q) use ($since) {
                    $q->where('created_at', '>=', $since)
                        ->orWhere('updated_at', '>=', $since);
                });
            })
            ->paginate();

        $resource = PersonRoleResource::collection($roles);
        $result = $resource->response()->getData(true);
        if ($roles->currentPage() >= $roles->lastPage()) {
            $result['last_synced_at'] = now()->toDateTimeString();
        }

        return response()->json($result);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
