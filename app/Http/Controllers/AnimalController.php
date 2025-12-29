<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\Premise;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\AnimalRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AnimalsExport;
use App\Exports\AnimalsTemplateExport;
use App\Exports\AnimalsImportErrorsExport;
use App\Imports\AnimalsImport;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AnimalController extends Controller
{

    /* public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth');

        // Middleware pour permissions CRUD
        $table =  Animal::getTableName();
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
        $communityId = getPermissionsTeamId();
        $animals = Animal::query()->
            whereHas('premise', function ($q) use ($communityId) {
                $q->where('community_id', $communityId);
            })
            ->search($q)
            ->orderByDesc('created_at')
            ->paginate()
            ->appends(['q' => $q]);

        return view('animal.index', compact('animals', 'q'))
            ->with('i', ($request->input('page', 1) - 1) * $animals->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $animal = new Animal();
        $premises = Premise::where('community_id', session('selected_community'))->get();

        return view('animal.create', compact('animal', 'premises'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnimalRequest $request): RedirectResponse
    {
        $all = $request->validated();
        $all['uid'] = \Illuminate\Support\Str::uuid()->toString();

        Animal::create($all);

        return Redirect::route('animals.index')
            ->with('success', 'Animal créé avec succès !.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $animal = Animal::findOrFail($id);

        return view('animal.show', compact('animal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $animal = Animal::findOrFail($id);
        $premises = Premise::where('community_id', session('selected_community'))->get();

        return view('animal.edit', compact('animal', 'premises'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnimalRequest $request, Animal $animal): RedirectResponse
    {
        $all = $request->validated();
        $animal->update($all);

        return Redirect::route('animals.index')
            ->with('success', 'Animal mise à jour avec succès !');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $data = Animal::findOrFail($id);
            $data->delete();

            return Redirect::route('animals.index')
                ->with('success', 'Animal supprimé avec succès !');
        } catch (\Throwable $th) {
            return Redirect::back()
                ->with('error', "Impossible de supprimer cette donnée car elle est liée à d'autres enregistrements.");
        }

    }
    public function export()
    {
        return Excel::download(new AnimalsExport, 'animals.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new AnimalsTemplateExport, 'animals_template.xlsx');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $data = Excel::toCollection(new AnimalsImport, $request->file('file'))->first();

        $validData = [];
        $invalidData = [];

        foreach ($data as $index => $row) {
            // cast all values to string
            $row = array_map('strval', $row->toArray());
            $rowData = [
                'premises_code' => $row['premises_code'] ?? null,
                'species' => $row['species'] ?? null,
                'sex' => $row['sex'] ?? null,
                'birth_date' => $row['birth_date'] ?? null,
                'life_status' => $row['life_status'] ?? null,
            ];

            $validator = Validator::make($rowData, [
                'premises_code' => 'required|exists:premises,code',
                'species' => 'required|in:OVINE,CAPRINE',
                'sex' => 'required|in:M,F',
                'birth_date' => 'nullable|date',
                'life_status' => 'required|in:ALIVE,DEAD,SOLD',
            ]);

            if ($validator->fails()) {
                $invalidData[] = [
                    'row' => $index + 2, // +2 because 1-based index and header row
                    'data' => $rowData,
                    'errors' => $validator->errors()->all(),
                ];
            } else {
                $validData[] = $rowData;
            }
        }

        session()->put('import_valid_data', $validData);
        session()->put('import_invalid_data', $invalidData);

        return redirect()->route('animals.import.preview');
    }

    public function preview()
    {
        $validData = session()->get('import_valid_data', []);
        $invalidData = session()->get('import_invalid_data', []);

        return view('animal.import_preview', compact('validData', 'invalidData'));
    }

    public function confirmImport()
    {
        $validData = session()->get('import_valid_data', []);

        foreach ($validData as $data) {
            $premise = Premise::where('code', $data['premises_code'])->first();

            if ($premise) {
                Animal::create([
                    'uid' => Str::uuid(),
                    'created_by' => auth()->id(),
                    'premises_id' => $premise->id,
                    'species' => $data['species'],
                    'sex' => $data['sex'],
                    'birth_date' => $data['birth_date'],
                    'life_status' => $data['life_status'],
                ]);
            }
        }

        session()->forget(['import_valid_data', 'import_invalid_data']);

        return redirect()->route('animals.index')->with('success', count($validData) . ' animaux importés avec succès.');
    }

    public function downloadErrors()
    {
        $invalidData = session()->get('import_invalid_data', []);
        return Excel::download(new AnimalsImportErrorsExport($invalidData), 'import_errors.xlsx');
    }
}
