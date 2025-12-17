<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetCommunityContext;
use App\Models\Community;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CommunityRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CommunityController extends Controller
{

    public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth');
        $this->middleware(SetCommunityContext::class);

        // Middleware pour permissions CRUD
        $table = Community::getTableName();
        $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show']);
        $this->middleware("permission:create $table")->only(['create', 'store']);
        $this->middleware("permission:update $table")->only(['edit', 'update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // retourn juste les communities dont l'utilisateur connecté est membre
        $q = $request->string('q')->toString();
        $com = getPermissionsTeamId();
        setPermissionsTeamId(null);
        $communities = Community::query();
        if (!auth()->user()->hasRole('Super-admin')) {
            # code...
            $communities = $communities->whereHas('members', function ($query) {
                $query->where('users.id', auth()->id());
            });
        }
        setPermissionsTeamId($com);
        $communities = $communities
            ->orderByDesc('created_at')
            ->paginate()
            ->appends(['q' => $q]);

        return view('community.index', compact('communities', 'q'))
            ->with('i', ($request->input('page', 1) - 1) * $communities->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $community = new Community();
        $countries = \App\Models\Country::where('is_active', true)->get();

        return view('community.create', compact('community', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommunityRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['creation_date'] = now();
        Community::create($data);

        return Redirect::route('communities.index')
            ->with('success', 'Community créé avec succès !.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $community = Community::findOrFail($id);
        $members = $community->members()->paginate(10);

        return view('community.show', compact('community', 'members'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $community = Community::findOrFail($id);
        $countries = \App\Models\Country::where('is_active', true)->get();

        return view('community.edit', compact('community', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommunityRequest $request, Community $community): RedirectResponse
    {
        $all = $request->validated();
        $community->update($all);

        return Redirect::route('communities.index')
            ->with('success', 'Community mise à jour avec succès !');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $data = Community::findOrFail($id);
            $data->delete();

            return Redirect::route('communities.index')
                ->with('success', 'Community supprimé avec succès !');
        } catch (\Throwable $th) {
            return Redirect::back()
                ->with('error', "Impossible de supprimer cette donnée car elle est liée à d'autres enregistrements.");
        }

    }

    public function myCommunities(): View
    {
        $user = auth()->user();
        $communities = $user->communities;
        return view('community.my-communities', compact('communities'));
    }

    public function selectCommunity(Request $request): RedirectResponse
    {
        $request->validate([
            'community_id' => 'required|exists:communities,id',
        ]);

        $communityId = $request->input('community_id');
        $user = auth()->user();

        // Verify membership
        if (!$user->communities()->where('communities.id', $communityId)->exists()) {
            return back()->with('error', 'You are not a member of this community.');
        }

        session(['selected_community' => $communityId]);

        return redirect()->route('home')->with('success', 'Workspace selected successfully.');
    }
}
