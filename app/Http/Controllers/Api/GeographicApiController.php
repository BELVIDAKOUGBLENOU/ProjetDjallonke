<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\District;
use App\Models\SubDistrict;
use App\Models\Village;
use Illuminate\Http\Request;
use App\Http\Requests\CountryRequest;
use App\Http\Requests\DistrictRequest;
use App\Http\Requests\SubDistrictRequest;
use App\Http\Requests\VillageRequest;

class GeographicApiController extends Controller
{
    // --- Fetching ---

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
