<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CommunityMembership;
use App\Http\Middleware\SetCommunityContext;

class CommunityMembershipController extends Controller
{
    public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth');
        $this->middleware(SetCommunityContext::class);
        $this->middleware(function ($request, $next) {
            dd(auth()->user()->getRoleNames()->toArray());
        });

        // Middleware pour permissions CRUD
        $table = Community::getTableName();
        $this->middleware("permission:add member $table")->only('store');
        $this->middleware("permission:update member role $table")->only('update');
        $this->middleware("permission:remove member $table")->only('destroy');
    }
    public function store(Request $request, Community $community)
    {
        $isSuperAdmin = auth()->user()?->hasRole('Super-admin') ?? false;
        $request->validate([
            'role' => 'required|in:FARMER,VET,TECHNICIAN,RESEARCHER' . ($isSuperAdmin ? ',COMMUNITY_ADMIN' : ''),
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|required_if:role,FARMER|string|max:20',
            'nationalId' => 'nullable|required_if:role,FARMER|string|max:50',
        ], [
            'role.in' => 'The selected role is invalid.' . ($isSuperAdmin ? '' : ' Only FARMER, VET, TECHNICIAN, and RESEARCHER roles are allowed.'),
        ]);

        try {
            Community::addMember($community->id, $request->all());
            return back()->with('success', 'Member added successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error adding member: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Community $community, $userId)
    {
        $request->validate([
            'role' => 'required|in:COMMUNITY_ADMIN,FARMER,VET,TECHNICIAN,RESEARCHER',
        ]);
        DB::beginTransaction();
        $membership = CommunityMembership::where('community_id', $community->id)
            ->where('user_id', $userId)
            ->firstOrFail();
        setPermissionsTeamId($community->id);
        $membership->user->removeRole($membership->role);

        $membership->update(['role' => $request->role]);
        $membership->user->assignRole($request->role);
        DB::commit();

        return back()->with('success', 'Member role updated successfully.');
    }

    public function destroy(Community $community, $userId)
    {
        Community::removeMember($community->id, $userId);
        return back()->with('success', 'Member removed successfully.');
    }
}
