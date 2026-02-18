<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\Village;
use Illuminate\Http\Request;
use App\Http\Resources\VillageResource;
use App\Http\Controllers\Controller;
use App\Http\Middleware\SetCommunityContextFrontend;

class VillageRemoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(SetCommunityContextFrontend::class);

        // Permissions
        $table = Village::getTableName();
        $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show']);
        $this->middleware("permission:create $table")->only(['store']);
        $this->middleware("permission:update $table")->only(['update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }

    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $query = Village::query();

        if ($q) {
            $query->search($q);
        }

        // Filter by sub_district if provided
        if ($request->has('sub_district_id')) {
            $query->where('sub_district_id', $request->input('sub_district_id'));
        }

        $villages = $query->orderBy('name')
            ->paginate(20)
            ->appends(['q' => $q]);

        return VillageResource::collection($villages);
    }

    public function indexBySubDistrict(string $subDistrict)
    {
        $villages = Village::where('sub_district_id', $subDistrict)->orderBy('name')->get();
        return VillageResource::collection($villages);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255', // Removed unique constraint as multiple villages can have same name in diff sub-districts, usually unique per sub-district
            'local_code' => 'nullable|string|max:50',
            'sub_district_id' => 'required|exists:sub_districts,id',
        ]);

        $village = Village::create($validated);

        return new VillageResource($village);
    }

    public function show(string $village)
    {
        $village = Village::findOrFail($village);
        return new VillageResource($village);
    }

    public function update(Request $request, string $village)
    {
        $village = Village::findOrFail($village);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'local_code' => 'nullable|string|max:50',
            'sub_district_id' => 'sometimes|exists:sub_districts,id',
        ]);

        $village->update($validated);

        return new VillageResource($village);
    }

    public function destroy(string $village)
    {
        $village = Village::findOrFail($village);

        // Check for related records if any (e.g. communities, premises)
        // Assuming no strict deletion blocker unless specified
        // But let's check basic sanity if it's used somewhere
        // if ($village->premises()->exists()) ...

        $village->delete();

        return response()->noContent();
    }
}
