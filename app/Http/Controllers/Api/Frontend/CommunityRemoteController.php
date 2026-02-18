<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetCommunityContextFrontend;
use App\Http\Requests\CommunityRequest;
use App\Http\Resources\CommunityResource;
use App\Models\Community;
use App\Models\CommunityMembership;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CommunityRemoteController extends Controller
{
    //constructeur pour appliquer les middlewares d'authentification et de contexte communautaire
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(SetCommunityContextFrontend::class)->except(['myCommunities']);
        // Forcer pour avoir les permissions globales (team_id = 0) pour accéder à toutes les communautés
        // setPermissionsTeamId(0);
        // Middleware pour permissions CRUD
        $table = Community::getTableName();
        $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show']);
        $this->middleware("permission:create $table")->only(['store']);
        $this->middleware("permission:update $table")->only(['update']);
        $this->middleware("permission:delete $table")->only('destroy');

        $this->middleware("permission:add member $table")->only('addMember');
        $this->middleware("permission:update member role $table")->only('updateMember');
        $this->middleware("permission:remove member $table")->only('removeMember');
    }

    /**
     * Get list of communities for the current user
     */
    public function myCommunities()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Ensure we are in global scope or similar to fetch all
        // Actually, without SetCommunityContextFrontend, getPermissionsTeamId might be null or default
        // But relation user->communities works regardless of permission content usually,
        // unless GlobalScope is applied on Community model.

        $communities = $user->communities()->with('country')->orderByDesc('created_at')->get();
        return CommunityResource::collection($communities);
    }

    public function addMember(Request $request, Community $community)
    {
        $isSuperAdmin = auth()->user()?->hasRole('Super-admin') ?? false;
        $request->validate([
            'role' => 'required|in:FARMER,VET,TECHNICIAN,RESEARCHER' . ($isSuperAdmin ? ',COMMUNITY_ADMIN' : ''),
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|required_if:role,FARMER|string|max:20',
            'nationalId' => 'nullable|required_if:role,FARMER|string|max:50',
        ], [
            'role.in' => 'The selected role is invalid.',
        ]);

        try {
            Community::addMember($community->id, $request->all());
            return response()->json(['message' => 'Member added successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error adding member: ' . $e->getMessage()], 422);
        }
    }

    public function updateMember(Request $request, Community $community, $userId)
    {
        $request->validate([
            'role' => 'required|in:COMMUNITY_ADMIN,FARMER,VET,TECHNICIAN,RESEARCHER',
        ]);

        try {
            DB::beginTransaction();
            $membership = CommunityMembership::where('community_id', $community->id)
                ->where('user_id', $userId)
                ->firstOrFail();

            // Set context explicitly for role management
            setPermissionsTeamId($community->id);

            // Remove old role then add new one
            if ($membership->user) {
                $membership->user->removeRole($membership->role);
                $membership->user->assignRole($request->role);
            }

            $membership->update(['role' => $request->role]);

            DB::commit();
            return response()->json(['message' => 'Member role updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error updating member role: ' . $e->getMessage()], 422);
        }
    }

    public function removeMember(Community $community, $userId)
    {
        try {
            Community::removeMember($community->id, $userId);
            return response()->json(['message' => 'Member removed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error removing member: ' . $e->getMessage()], 422);
        }
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teamID = getPermissionsTeamId();
        $search = request()->query('search');

        $query = Community::query()
            ->when(!$search, function ($query) use ($teamID) {
                // Si pas de recherche, filtres pour l'utilisateur
                if ($teamID !== 0) {
                    $query->whereHas('members', function ($q) {
                        $q->where('users.id', auth()->id());
                    });
                }
            })

            ->with('country', 'creator')
            ->search($search)
            ->orderBy('id', 'ASC'); // Pour l instant on trie par ID mais on peu changer

        $data = $query->paginate(10); // Un peu plus de pagination que 1 est mieux
        return CommunityResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommunityRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['creation_date'] = now();

        $community = Community::create($data);

        return new CommunityResource($community);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $community = Community::with(['country', 'creator', 'members'])->findOrFail($id);

        return new CommunityResource($community);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommunityRequest $request, string $id)
    {
        $community = Community::findOrFail($id);
        $all = $request->validated();

        $community->update($all);

        return new CommunityResource($community);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $community = Community::findOrFail($id);
            $community->delete();

            return response()->json(['message' => 'Community supprimée avec succès'], Response::HTTP_NO_CONTENT);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "Impossible de supprimer cette donnée car elle est liée à d'autres enregistrements.",
                'error' => $th->getMessage()
            ], Response::HTTP_CONFLICT);
        }
    }

}
