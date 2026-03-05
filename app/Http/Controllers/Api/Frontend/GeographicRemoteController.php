<?php

namespace App\Http\Controllers\Api\Frontend;
use App\Http\Controllers\Controller;
use App\Http\Middleware\SetCommunityContextFrontend;
use App\Models\Country;
use App\Models\District;
use App\Models\SubDistrict;
use App\Models\Village;
use Illuminate\Http\Request;
use App\Http\Requests\CountryRequest;
use App\Http\Requests\DistrictRequest;
use App\Http\Requests\SubDistrictRequest;
use App\Http\Requests\VillageRequest;
class GeographicRemoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware(SetCommunityContextFrontend::class);

        // Countries
        $this->middleware('permission:list countries')->only(['countries']);
        $this->middleware('permission:create countries')->only(['storeCountry']);
        $this->middleware('permission:update countries')->only(['updateCountry']);
        $this->middleware('permission:delete countries')->only(['destroyCountry']);

        // Districts
        $this->middleware('permission:list districts')->only(['districts']);
        $this->middleware('permission:create districts')->only(['storeDistrict']);
        $this->middleware('permission:update districts')->only(['updateDistrict']);
        $this->middleware('permission:delete districts')->only(['destroyDistrict']);

        // SubDistricts
        $this->middleware('permission:list sub_districts')->only(['subDistricts']);
        $this->middleware('permission:create sub_districts')->only(['storeSubDistrict']);
        $this->middleware('permission:update sub_districts')->only(['updateSubDistrict']);
        $this->middleware('permission:delete sub_districts')->only(['destroySubDistrict']);

        // Villages
        $this->middleware('permission:list villages')->only(['villages']);
        $this->middleware('permission:create villages')->only(['storeVillage']);
        $this->middleware('permission:update villages')->only(['updateVillage']);
        $this->middleware('permission:delete villages')->only(['destroyVillage']);
    }
    public function countries(Request $request)
    {
        $query = Country::query()->orderBy('name')->where("is_active", true);
        if ($request->has('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }
        return response()->json($query->get());
    }

    public function districts(Country $country)
    {
        return response()->json($country->districts()->orderBy('name')->get());
    }

    public function subDistricts(District $district)
    {
        return response()->json($district->subDistricts()->orderBy('name')->get());
    }

    public function villages(SubDistrict $subDistrict)
    {
        return response()->json($subDistrict->villages()->orderBy('name')->get());
    }

    // --- CRUD Country ---

    public function storeCountry(CountryRequest $request)
    {
        $country = Country::create($request->validated());
        return response()->json($country);
    }

    public function updateCountry(CountryRequest $request, Country $country)
    {
        $country->update($request->validated());
        return response()->json($country);
    }

    public function destroyCountry(Country $country)
    {
        if ($country->districts()->exists()) {
            return response()->json(['message' => 'Impossible de supprimer un pays contenant des districts.'], 422);
        }
        $country->delete();
        return response()->json(['message' => 'Supprimé']);
    }

    // --- CRUD District ---

    public function storeDistrict(DistrictRequest $request)
    {
        $district = District::create($request->validated());
        return response()->json($district);
    }

    public function updateDistrict(DistrictRequest $request, District $district)
    {
        $district->update($request->validated());
        return response()->json($district);
    }

    public function destroyDistrict(District $district)
    {
        if ($district->subDistricts()->exists()) {
            return response()->json(['message' => 'Impossible de supprimer un district contenant des sous-districts.'], 422);
        }
        $district->delete();
        return response()->json(['message' => 'Supprimé']);
    }

    // --- CRUD SubDistrict ---

    public function storeSubDistrict(SubDistrictRequest $request)
    {
        $subDistrict = SubDistrict::create($request->validated());
        return response()->json($subDistrict);
    }

    public function updateSubDistrict(SubDistrictRequest $request, SubDistrict $subDistrict)
    {
        $subDistrict->update($request->validated());
        return response()->json($subDistrict);
    }

    public function destroySubDistrict(SubDistrict $subDistrict)
    {
        if ($subDistrict->villages()->exists()) {
            return response()->json(['message' => 'Impossible de supprimer un sous-district contenant des villages.'], 422);
        }
        $subDistrict->delete();
        return response()->json(['message' => 'Supprimé']);
    }

    // --- CRUD Village ---

    public function storeVillage(VillageRequest $request)
    {
        $village = Village::create($request->validated());
        return response()->json($village);
    }

    public function updateVillage(VillageRequest $request, Village $village)
    {
        $village->update($request->validated());
        return response()->json($village);
    }

    public function destroyVillage(Village $village)
    {
        $village->delete();
        return response()->json(['message' => 'Supprimé']);
    }
}
