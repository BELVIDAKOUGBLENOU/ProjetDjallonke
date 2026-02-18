<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\District;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DistrictResource;
use App\Http\Middleware\SetCommunityContextFrontend;

class DistrictRemoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(SetCommunityContextFrontend::class);

        // Permissions
        $table = District::getTableName();
        $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show']);
        $this->middleware("permission:create $table")->only(['store']);
        $this->middleware("permission:update $table")->only(['update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }

    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $query = District::query();

        if ($q) {
            $query->search($q);
        }

        // Filter by country if provided
        if ($request->has('country_id')) {
            $query->where('country_id', $request->input('country_id'));
        }

        $districts = $query->orderBy('name')
            ->paginate(20)
            ->appends(['q' => $q]);

        return DistrictResource::collection($districts);
    }

    public function indexByCountry(string $country)
    {
        $districts = District::where('country_id', $country)->orderBy('name')->get();
        return DistrictResource::collection($districts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:districts,name',
            'country_id' => 'required|exists:countries,id',
        ]);

        $district = District::create($validated);

        return new DistrictResource($district);
    }

    public function show(string $district)
    {
        $district = District::findOrFail($district);
        return new DistrictResource($district);
    }

    public function update(Request $request, string $district)
    {
        $district = District::findOrFail($district);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:districts,name,' . $district->id,
            'country_id' => 'sometimes|exists:countries,id',
        ]);

        $district->update($validated);

        return new DistrictResource($district);
    }

    public function destroy(string $district)
    {
        $district = District::findOrFail($district);

        if ($district->subDistricts()->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer ce district car il contient des sous-districts.'
            ], 422);
        }

        $district->delete();

        return response()->noContent();
    }
}
