<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\District;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\DistrictRequest;
use Illuminate\Support\Facades\Redirect;

class DistrictController extends Controller
{

    public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth');

        // Middleware pour permissions CRUD
        $table = District::getTableName();
        $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show']);
        $this->middleware("permission:create $table")->only(['create', 'store']);
        $this->middleware("permission:update $table")->only(['edit', 'update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        $districts = District::query()
            ->search($q)
            ->orderByDesc('created_at')
            ->paginate()
            ->appends(['q' => $q]);


        return view('district.index', compact('districts', 'q'))
            ->with('i', ($request->input('page', 1) - 1) * $districts->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $district = new District();
        $countries = Country::where('is_active', true)->get();


        return view('district.create', compact('district', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DistrictRequest $request): RedirectResponse
    {
        $all = $request->validated();
        District::create($all);

        return Redirect::route('districts.index')
            ->with('success', 'District créé avec succès !.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $district = District::findOrFail($id);

        return view('district.show', compact('district'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $district = District::findOrFail($id);
        $countries = Country::where('is_active', true)->get();


        return view('district.edit', compact('district', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DistrictRequest $request, District $district): RedirectResponse
    {
        $all = $request->validated();
        $district->update($all);

        return Redirect::route('districts.index')
            ->with('success', 'District mise à jour avec succès !');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $data = District::findOrFail($id);
            $data->delete();

            return Redirect::route('districts.index')
                ->with('success', 'District supprimé avec succès !');
        } catch (\Throwable $th) {
            return Redirect::back()
                ->with('error', "Impossible de supprimer cette donnée car elle est liée à d'autres enregistrements.");
        }

    }
}
