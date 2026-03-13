<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Exports\PersonsExport;
use App\Http\Controllers\Controller;
use App\Http\Middleware\SetCommunityContextFrontend;
use App\Http\Requests\PersonRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class PersonRemoteController extends Controller
{
    public function __construct()
    {
        $table = Person::getTableName();
        $this->middleware(SetCommunityContextFrontend::class);
        $this->middleware("permission:list $table")->only(['index', 'export']);
        $this->middleware("permission:view $table")->only(['show']);
        $this->middleware("permission:create $table")->only(['store']);
        $this->middleware("permission:update $table")->only(['update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }

    /**
     * Export persons
     */
    public function export()
    {
        $communityId = getPermissionsTeamId();
        if (! $communityId || $communityId == 0) {
            return response()->json(['message' => 'Community context required for export.'], 403);
        }

        return Excel::download(new PersonsExport($communityId), 'persons.xlsx');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $communityId = getPermissionsTeamId(); // Using global helper if available, otherwise assume middleware sets it on user/context

        // If 'active_only' is used somewhere, handle it. Here just search.

        $query = Person::query()
            ->withCount([
                'personRoles as owned_animals_count' => function ($query) {
                    $query->where('role_type', 'OWNER');
                },
            ])
            ->withCount('personRoles as related_animals_count');

        if ($communityId && $communityId != 0) {
            $query->whereHas('personRoles', function ($q) use ($communityId) {
                $q->whereHas('animal', function ($q) use ($communityId) {
                    $q->whereHas('premise', function ($q) use ($communityId) {
                        $q->where('community_id', $communityId);
                    });
                });
            });
            // Note: The logic from blade controller seemed to filter by community via complex relation.
            // Replicating it here.
        }

        if ($q) {
            $query->search($q);
        }

        $people = $query->latest()->paginate(10);

        return PersonResource::collection($people);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PersonRequest $request)
    {
        $data = $request->validated();
        $data['uid'] = Str::uuid();
        $person = Person::create($data);

        return new PersonResource($person);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $person = Person::findOrFail($id);

        return new PersonResource($person);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PersonRequest $request, string $id)
    {
        $person = Person::findOrFail($id);
        $person->update($request->validated());

        return new PersonResource($person);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $person = Person::findOrFail($id);
        $person->delete();

        return response()->json(null, 204);
    }
}
