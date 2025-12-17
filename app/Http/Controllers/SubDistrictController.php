<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\SubDistrict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\SubDistrictRequest;

class SubDistrictController extends Controller
{

    /* public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth');

        // Middleware pour permissions CRUD
        $table =  SubDistrict::getTableName();
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
        $subDistricts = SubDistrict::query()
            ->with(['district.country'])
            ->search($q)
            ->orderByDesc('created_at')
            ->paginate()
            ->appends(['q' => $q]);

        return view('sub-district.index', compact('subDistricts', 'q'))
            ->with('i', ($request->input('page', 1) - 1) * $subDistricts->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $subDistrict = new SubDistrict();
        $districts = \App\Models\District::join('countries', 'districts.country_id', '=', 'countries.id')
            ->select('districts.id', DB::raw("CONCAT(districts.name, ' (Pays : ', countries.name, ')') as name"))
            ->get();

        return view('sub-district.create', compact('subDistrict', 'districts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SubDistrictRequest $request): RedirectResponse
    {
        $all = $request->validated();
        SubDistrict::create($all);

        return Redirect::route('sub-districts.index')
            ->with('success', 'SubDistrict créé avec succès !.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $subDistrict = SubDistrict::findOrFail($id);

        return view('sub-district.show', compact('subDistrict'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $subDistrict = SubDistrict::findOrFail($id);
        $districts = \App\Models\District::join('countries', 'districts.country_id', '=', 'countries.id')
            ->select('districts.id', DB::raw("CONCAT(districts.name, ' (Pays : ', countries.name, ')') as name"))
            ->get();

        return view('sub-district.edit', compact('subDistrict', 'districts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SubDistrictRequest $request, SubDistrict $subDistrict): RedirectResponse
    {
        $all = $request->validated();
        $subDistrict->update($all);

        return Redirect::route('sub-districts.index')
            ->with('success', 'SubDistrict mise à jour avec succès !');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $data = SubDistrict::findOrFail($id);
            $data->delete();

            return Redirect::route('sub-districts.index')
                ->with('success', 'SubDistrict supprimé avec succès !');
        } catch (\Throwable $th) {
            return Redirect::back()
                ->with('error', "Impossible de supprimer cette donnée car elle est liée à d'autres enregistrements.");
        }

    }
}
