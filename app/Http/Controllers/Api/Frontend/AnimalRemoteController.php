<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetCommunityContextFrontend;
use App\Http\Requests\AnimalRequest;
use App\Http\Resources\AnimalResource;
use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AnimalRemoteController extends Controller
{
    public function __construct()
    {
        $table = Animal::getTableName();
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
        $q = $request->string('q')->toString();
        $limit = $request->integer('limit', 10);
        $communityId = getPermissionsTeamId(); // Or context

        $query = Animal::query();

        if ($communityId) {
            $query->whereHas('premise', function ($q) use ($communityId) {
                $q->where('community_id', $communityId);
            });
        }

        if ($q) {
            $query->search($q);
        }

        $animals = $query->with(['premise', 'creator', 'personRoles.person', 'identifiers'])->latest()->paginate($limit);

        return AnimalResource::collection($animals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnimalRequest $request)
    {
        $data = $request->validated();

        $data['created_by'] = Auth::id();
        // Generate UID: uuid + '-' + userId
        $data['uid'] = Str::uuid() . '-' . Auth::id();

        $animal = Animal::create($data);

        return new AnimalResource($animal);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $animal = Animal::with(['premise', 'creator'])->findOrFail($id);

        return new AnimalResource($animal);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnimalRequest $request, string $id)
    {
        $animal = Animal::findOrFail($id);
        $animal->update($request->validated());

        return new AnimalResource($animal);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $animal = Animal::findOrFail($id);
        $animal->delete();

        return response()->json(null, 204);
    }
}
