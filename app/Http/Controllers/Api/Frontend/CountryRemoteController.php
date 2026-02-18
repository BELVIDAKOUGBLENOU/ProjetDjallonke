<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Middleware\SetCommunityContextFrontend;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryRemoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(SetCommunityContextFrontend::class);


        $table = Country::getTableName();
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
        $active_only = $request->boolean('active_only', false);
        $imbriqued = $request->boolean('imbriqued', false);
        $query = Country::query();

        if ($q) {
            $query->search($q);
        }
        if ($active_only) {
            $query->where('is_active', true);
        }
        if ($imbriqued) {
            $query->with('districts.subDistricts.villages');
        }

        $countries = $query->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(20)
            ->appends(['q' => $q]);
        $resource = CountryResource::collection($countries);
        $resource->each(fn($r) => $r->setImbriqued($imbriqued));
        return ($resource);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:countries,name',
            'code_iso' => 'required|string|min:2|max:2|unique:countries,code_iso',
            'emoji' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        $country = Country::create($validated);

        return new CountryResource($country);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $country)
    {
        $country = Country::where('id', $country)->firstOrFail();
        return new CountryResource($country);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $country)
    {
        $country = Country::where('id', $country)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:countries,name,' . $country->id,
            'code_iso' => 'required|string|min:2|max:2|unique:countries,code_iso,' . $country->id,
            'emoji' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ]);

        $country->update($validated);

        return new CountryResource($country);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $country)
    {
        $country = Country::where('id', $country)->firstOrFail();

        // Basic check for relations before delete
        if ($country->communities()->exists() || $country->districts()->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer ce pays car il est lié à d\'autres enregistrements.'
            ], 422);
        }

        $country->delete();

        return response()->noContent();
    }
}

