<?php

namespace App\Http\Controllers;

use App\Models\Village;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\VillageRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use Illuminate\Support\Facades\DB;

class VillageController extends Controller
{

    /* public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth');

        // Middleware pour permissions CRUD
        $table =  Village::getTableName();
        $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show']);
        $this->middleware("permission:create $table")->only(['create', 'store']);
        $this->middleware("permission:update $table")->only(['edit', 'update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }*/
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        $villages = Village::query()
            ->with(['subDistrict.district.country'])
            ->search($q)
            ->orderByDesc('created_at')
            ->paginate()
            ->appends(['q' => $q]);

        return view('village.index', compact('villages', 'q'))
            ->with('i', ($request->input('page', 1) - 1) * $villages->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $village = new Village();
        $subDistricts = \App\Models\SubDistrict::join('districts', 'sub_districts.district_id', '=', 'districts.id')
            ->join('countries', 'districts.country_id', '=', 'countries.id')
            ->select('sub_districts.id', DB::raw("CONCAT(sub_districts.name, ' (District : ', districts.name, ', Pays : ', countries.name, ')') as name"))
            ->get();

        return view('village.create', compact('village', 'subDistricts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VillageRequest $request): RedirectResponse
    {
        $all = $request->validated();
        Village::create($all);

        return Redirect::route('villages.index')
            ->with('success', 'Village créé avec succès !.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $village = Village::findOrFail($id);

        return view('village.show', compact('village'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $village = Village::findOrFail($id);
        $subDistricts = \App\Models\SubDistrict::join('districts', 'sub_districts.district_id', '=', 'districts.id')
            ->join('countries', 'districts.country_id', '=', 'countries.id')
            ->select('sub_districts.id', DB::raw("CONCAT(sub_districts.name, ' (District : ', districts.name, ', Pays : ', countries.name, ')') as name"))
            ->get();

        return view('village.edit', compact('village', 'subDistricts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VillageRequest $request, Village $village): RedirectResponse
    {
        $all = $request->validated();
        $village->update($all);

        return Redirect::route('villages.index')
            ->with('success', 'Village mise à jour avec succès !');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $data = Village::findOrFail($id);
            $data->delete();

            return Redirect::route('villages.index')
                ->with('success', 'Village supprimé avec succès !');
        } catch (\Throwable $th) {
            return Redirect::back()
                ->with('error', "Impossible de supprimer cette donnée car elle est liée à d'autres enregistrements.");
        }

    }
}
