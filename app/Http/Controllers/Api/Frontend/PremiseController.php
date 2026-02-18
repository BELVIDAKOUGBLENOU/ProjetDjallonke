<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetCommunityContextFrontend;
use App\Http\Requests\PremiseRequest;
use App\Http\Resources\PremiseResource;
use App\Models\Premise;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PremiseController extends Controller
{
    public function __construct()
    {
        $table = Premise::getTableName();
        $this->middleware(SetCommunityContextFrontend::class);
        $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show']);
        $this->middleware("permission:create $table")->only(['store']);
        $this->middleware("permission:update $table")->only(['update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->input('q');

        $query = Premise::query()
            ->with(['village', 'creator']);

        if ($q) {
            $query->search($q);
        }

        // Filter by community unless super admin (team id 0)
        // Usually, getPermissionsTeamId() returns current team id set by middleware
        $teamId = getPermissionsTeamId();
        if ($teamId && $teamId != 0) {
            $query->where('community_id', $teamId);
        }

        $premises = $query->orderByDesc('created_at')
            ->paginate($request->input('per_page', 15));

        return PremiseResource::collection($premises);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PremiseRequest $request)
    {
        $data = $request->validated();
        if (getPermissionsTeamId() == 0) {
            return response()->json(['message' => 'Super admin cannot create premises without community context. '], 422);
        }
        $data['created_by'] = auth()->id();
        $data['community_id'] = getPermissionsTeamId();

        if (empty($data['community_id'])) {
            // If for some reason team ID is 0 or null, we might want to throw error or require it in body?
            // But usually frontend sends X-Community-ID header handled by middleware.
            // If super admin creating, maybe they should specify community_id in body?
            // For now assume standard flow where header sets context.
            if ($request->has('community_id')) {
                $data['community_id'] = $request->input('community_id');
            }
        }

        $data['uid'] = Str::uuid()->toString();

        $premise = Premise::create($data);

        return new PremiseResource($premise->load(['village', 'creator']));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $premise)
    {
        // Policy or scope check?
        // With permission middleware, we know user can view premises.
        // We ensure premise belongs to current community context.
        $teamId = getPermissionsTeamId();
        $premise = Premise::where('uid', $premise)->orWhere('id', $premise)->firstOrFail();

        if ($teamId && $teamId != 0 && $premise->community_id != $teamId) {
            abort(403, 'Unauthorized access to this premise.');
        }

        return new PremiseResource($premise->load(['village', 'creator', 'keepers.person']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PremiseRequest $request, Premise $premise)
    {
        $teamId = getPermissionsTeamId();
        if ($teamId && $teamId != 0 && $premise->community_id != $teamId) {
            abort(403, 'Unauthorized access to this premise.');
        }

        $data = $request->validated();

        $premise->update($data);

        return new PremiseResource($premise->load(['village', 'creator']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Premise $premise)
    {
        $teamId = getPermissionsTeamId();
        if ($teamId && $teamId != 0 && $premise->community_id != $teamId) {
            abort(403, 'Unauthorized access to this premise.');
        }

        try {
            $premise->delete();
            return response()->json(['message' => 'Premise supprimé avec succès']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "Impossible de supprimer cette donnée car elle est liée à d'autres enregistrements."
            ], 422);
        }
    }
}
