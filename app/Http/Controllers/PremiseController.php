<?php

namespace App\Http\Controllers;

use App\Models\Premise;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\PremiseRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PremisesExport;
use App\Exports\PremisesTemplateExport;
use App\Exports\PremisesImportErrorsExport;
use App\Imports\PremisesImport;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PremiseController extends Controller
{

    public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth');
        $this->middleware('require.community');

        // Middleware pour permissions CRUD
        $table = Premise::getTableName();
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
        $premises = Premise::query()
            ->with(['village', 'creator'])
            ->search($q)
            ->where('community_id', getPermissionsTeamId())
            ->orderByDesc('created_at')
            ->paginate()
            ->appends(['q' => $q]);

        return view('premise.index', compact('premises', 'q'))
            ->with('i', ($request->input('page', 1) - 1) * $premises->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $premise = new Premise();
        $villages = \App\Models\Village::all();

        return view('premise.create', compact('premise', 'villages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PremiseRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['community_id'] = session('selected_community');
        $data['uid'] = \Illuminate\Support\Str::uuid()->toString();

        Premise::create($data);

        return Redirect::route('premises.index')
            ->with('success', 'Premise créé avec succès !.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $premise = Premise::with('keepers.person')->findOrFail($id);
        $persons = \App\Models\Person::all();

        return view('premise.show', compact('premise', 'persons'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $premise = Premise::findOrFail($id);
        $villages = \App\Models\Village::all();

        return view('premise.edit', compact('premise', 'villages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PremiseRequest $request, Premise $premise): RedirectResponse
    {
        $all = $request->validated();
        $premise->update($all);

        return Redirect::route('premises.index')
            ->with('success', 'Premise mise à jour avec succès !');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $data = Premise::findOrFail($id);
            $data->delete();

            return Redirect::route('premises.index')
                ->with('success', 'Premise supprimé avec succès !');
        } catch (\Throwable $th) {
            return Redirect::back()
                ->with('error', "Impossible de supprimer cette donnée car elle est liée à d'autres enregistrements.");
        }

    }

    public function export()
    {
        return Excel::download(new PremisesExport, 'premises.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new PremisesTemplateExport, 'premises_template.xlsx');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $data = Excel::toCollection(new PremisesImport, $request->file('file'))->first();

        $validData = [];
        $invalidData = [];

        foreach ($data as $index => $row) {
            $rowData = [
                'village_id' => $row['village_id'] ?? null,
                'code' => $row['code'] ?? null,
                'address' => $row['address'] ?? null,
                'gps_coordinates' => $row['gps_coordinates'] ?? null,
                'type' => $row['type'] ?? null,
                'health_status' => $row['health_status'] ?? null,
            ];

            $validator = Validator::make($rowData, [
                'village_id' => 'required|exists:villages,id',
                'code' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('premises', 'code'),
                ],
                'address' => 'nullable|string|max:255',
                'gps_coordinates' => 'nullable|string|max:255',
                'type' => 'required|string|max:255',
                'health_status' => 'nullable|string|max:255',
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

        session()->put('import_premises_valid_data', $validData);
        session()->put('import_premises_invalid_data', $invalidData);

        return redirect()->route('premises.import.preview');
    }

    public function preview()
    {
        $validData = session()->get('import_premises_valid_data', []);
        $invalidData = session()->get('import_premises_invalid_data', []);

        return view('premise.import_preview', compact('validData', 'invalidData'));
    }

    public function confirmImport()
    {
        $validData = session()->get('import_premises_valid_data', []);
        $createdBy = auth()->id();
        $communityId = session('selected_community');

        foreach ($validData as $data) {
            $data['created_by'] = $createdBy;
            $data['community_id'] = $communityId;
            Premise::create($data);
        }

        session()->forget(['import_premises_valid_data', 'import_premises_invalid_data']);

        return redirect()->route('premises.index')->with('success', count($validData) . ' premises importées avec succès.');
    }

    public function downloadErrors()
    {
        $invalidData = session()->get('import_premises_invalid_data', []);
        return Excel::download(new PremisesImportErrorsExport($invalidData), 'import_errors.xlsx');
    }
}
