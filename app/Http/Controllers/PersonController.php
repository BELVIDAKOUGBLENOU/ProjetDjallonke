<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PersonRequest;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PersonsExport;
use App\Exports\PersonsTemplateExport;
use App\Exports\PersonsImportErrorsExport;
use App\Imports\PersonsImport;
use Illuminate\Support\Facades\Validator;

class PersonController extends Controller
{

    public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth');

        // Middleware pour permissions CRUD
        $table = Person::getTableName();
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
        $communityId = getPermissionsTeamId();
        $people = Person::
            when($communityId, function ($query) use ($communityId) {
                $query->whereHas('personRoles', function ($q) use ($communityId) {
                    $q->whereHas('animal', function ($q) use ($communityId) {
                        $q->whereHas('premise', function ($q) use ($communityId) {
                            $q->where('community_id', $communityId);
                        });
                    });
                });
            })->
            search($q)
            ->orderByDesc('created_at')
            ->paginate()
            ->appends(['q' => $q]);

        return view('person.index', compact('people', 'q'))
            ->with('i', ($request->input('page', 1) - 1) * $people->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $person = new Person();

        return view('person.create', compact('person'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PersonRequest $request): RedirectResponse
    {
        $all = $request->validated();
        Person::create($all);

        return Redirect::route('people.index')
            ->with('success', 'Person créé avec succès !.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $person = Person::findOrFail($id);

        return view('person.show', compact('person'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $person = Person::findOrFail($id);

        return view('person.edit', compact('person'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PersonRequest $request, Person $person): RedirectResponse
    {
        $all = $request->validated();
        $person->update($all);

        return Redirect::route('people.index')
            ->with('success', 'Person mise à jour avec succès !');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $data = Person::findOrFail($id);
            if (User::where('person_id', $data->id)->exists()) {
                return Redirect::back()
                    ->with('error', "Impossible de supprimer cette donnée car elle est liée à un utilisateur.");
            }
            $data->delete();

            return Redirect::route('people.index')
                ->with('success', 'Person supprimé avec succès !');
        } catch (\Throwable $th) {
            return Redirect::back()
                ->with('error', "Impossible de supprimer cette donnée car elle est liée à d'autres enregistrements.");
        }

    }

    public function export()
    {
        return Excel::download(new PersonsExport, 'persons.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new PersonsTemplateExport, 'persons_template.xlsx');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $data = Excel::toCollection(new PersonsImport, $request->file('file'))->first();

        $validData = [];
        $invalidData = [];

        foreach ($data as $index => $row) {
            // cast all values to string
            $row = array_map('strval', $row->toArray());
            $rowData = [
                'name' => $row['name'] ?? null,
                'address' => $row['address'] ?? null,
                'phone' => $row['phone'] ?? null,
                'nationalId' => $row['nationalid'] ?? $row['national_id'] ?? null,
            ];
            // dd($rowData, $row);

            $validator = Validator::make($rowData, [
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'nationalId' => 'required|string|max:50',
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

        return redirect()->route('people.import.preview');
    }

    public function preview()
    {
        $validData = session()->get('import_valid_data', []);
        $invalidData = session()->get('import_invalid_data', []);

        return view('person.import_preview', compact('validData', 'invalidData'));
    }

    public function confirmImport()
    {
        $validData = session()->get('import_valid_data', []);

        foreach ($validData as $data) {
            Person::updateOrCreate(
                [
                    'nationalId' => $data['nationalId'],
                ],
                $data
            );
        }

        session()->forget(['import_valid_data', 'import_invalid_data']);

        return redirect()->route('people.index')->with('success', count($validData) . ' personnes importées avec succès.');
    }

    public function downloadErrors()
    {
        $invalidData = session()->get('import_invalid_data', []);
        return Excel::download(new PersonsImportErrorsExport($invalidData), 'import_errors.xlsx');
    }
}
