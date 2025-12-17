<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CountryRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CountryController extends Controller
{

    public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth');

        // Middleware pour permissions CRUD
        $table = Country::getTableName();
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
        $countries = Country::query()
            ->search($q)
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate()
            ->appends(['q' => $q]);

        return view('country.index', compact('countries', 'q'))
            ->with('i', ($request->input('page', 1) - 1) * $countries->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $country = new Country();

        return view('country.create', compact('country'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CountryRequest $request): RedirectResponse
    {
        $all = $request->validated();
        Country::create($all);

        return Redirect::route('countries.index')
            ->with('success', 'Country créé avec succès !.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $country = Country::findOrFail($id);

        return view('country.show', compact('country'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $country = Country::findOrFail($id);

        return view('country.edit', compact('country'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CountryRequest $request, Country $country): RedirectResponse
    {
        $all = $request->validated();
        $country->update($all);

        return Redirect::route('countries.index')
            ->with('success', 'Country mise à jour avec succès !');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $data = Country::findOrFail($id);
            $data->delete();

            return Redirect::route('countries.index')
                ->with('success', 'Country supprimé avec succès !');
        } catch (\Throwable $th) {
            return Redirect::back()
                ->with('error', "Impossible de supprimer cette donnée car elle est liée à d'autres enregistrements.");
        }

    }
}
