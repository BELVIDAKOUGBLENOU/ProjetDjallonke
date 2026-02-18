<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\SubDistrict;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubDistrictResource;
use App\Http\Middleware\SetCommunityContextFrontend;

class SubDistrictRemoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(SetCommunityContextFrontend::class);

        // Permissions
        $table = SubDistrict::getTableName();
        $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show']);
        $this->middleware("permission:create $table")->only(['store']);
        $this->middleware("permission:update $table")->only(['update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }

    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $query = SubDistrict::query();

        if ($q) {
            $query->search($q);
        }

        // Filter by district if provided
        if ($request->has('district_id')) {
            $query->where('district_id', $request->input('district_id'));
        }

        $subDistricts = $query->orderBy('name')
            ->paginate(20)
            ->appends(['q' => $q]);

        return SubDistrictResource::collection($subDistricts);
    }

    public function indexByDistrict(string $district)
    {
        $subDistricts = SubDistrict::where('district_id', $district)->orderBy('name')->get();
        return SubDistrictResource::collection($subDistricts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sub_districts,name',
            'district_id' => 'required|exists:districts,id',
        ]);

        $subDistrict = SubDistrict::create($validated);

        return new SubDistrictResource($subDistrict);
    }

    public function show(string $subDistrict)
    {
        $subDistrict = SubDistrict::findOrFail($subDistrict);
        return new SubDistrictResource($subDistrict);
    }

    public function update(Request $request, string $subDistrict)
    {
        $subDistrict = SubDistrict::findOrFail($subDistrict);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sub_districts,name,' . $subDistrict->id,
            'district_id' => 'sometimes|exists:districts,id',
        ]);

        $subDistrict->update($validated);

        return new SubDistrictResource($subDistrict);
    }

    public function destroy(string $subDistrict)
    {
        $subDistrict = SubDistrict::findOrFail($subDistrict);

        if ($subDistrict->villages()->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer ce sous-district car il contient des villages.'
            ], 422);
        }

        $subDistrict->delete();

        return response()->noContent();
    }
}
