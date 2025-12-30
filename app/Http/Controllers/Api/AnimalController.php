<?php

namespace App\Http\Controllers\Api;

use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnimalRequest;
use App\Http\Resources\AnimalResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Models\Premise;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Middleware\SetCommunityContextAPI;
use Illuminate\Routing\Controllers\HasMiddleware;

class AnimalController extends Controller
    // implements HasMiddleware
{
    public function __construct()
    {
        // Middleware pour authentification
        $this->middleware('auth:sanctum');
        $this->middleware(SetCommunityContextAPI::class);
        $this->middleware(function ($request, $next) {
            $animalId = $request->route('api_animal');
            if ($animalId) {
                $animal = ($animalId instanceof Animal) ? $animalId : Animal::findOrFail($animalId);
                setPermissionsTeamId($animal->community_id);
            }
            return $next($request);
        });


        // Middleware pour permissions CRUD
        $table = Animal::getTableName();
        // $this->middleware("permission:list $table")->only('index');
        $this->middleware("permission:view $table")->only(['show', 'getAllData']);
        $this->middleware("permission:create $table")->only(['create', 'store']);
        $this->middleware("permission:update $table")->only(['edit', 'update']);
        $this->middleware("permission:delete $table")->only('destroy');
    }

    // public function getAllData(Request $request): JsonResponse
    // {
    //     $communityId = getPermissionsTeamId();
    //     $animals = Animal::query()
    //         ->whereHas('premise', function ($q) use ($communityId) {
    //             $q->where('community_id', $communityId);
    //         })
    //         ->get();

    //     return response()->json(AnimalResource::collection($animals));
    // }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $communityId = getPermissionsTeamId();
        $since = $request->validate([
            'since' => 'nullable|date_format:Y-m-d H:i:s',
        ])['since'] ?? "1970-01-01 00:00:00";
        $animals = Animal::
            whereHas('premise', function ($q) use ($communityId) {
                $q->where('community_id', $communityId);
            })->
            when($since, function ($query, $since) {
                $query->where('updated_at', '>=', $since);
            })->
            paginate();

        $resource = AnimalResource::collection($animals);
        $result = $resource->response()->getData(true);
        // si on est à la derniere page , on ajoute les last_synced_at
        if ($animals->currentPage() >= $animals->lastPage()) {
            $result['last_synced_at'] = now()->toDateTimeString();
        }

        return response()->json($result);
    }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(AnimalRequest $request): JsonResponse
    // {
    //     $animal = Animal::create($request->validated());

    //     return response()->json(new AnimalResource($animal));
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(Animal $animal): JsonResponse
    // {
    //     return response()->json(new AnimalResource($animal));
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(AnimalRequest $request, Animal $animal): JsonResponse
    // {
    //     $animal->update($request->validated());

    //     return response()->json(new AnimalResource($animal));
    // }

    // /**
    //  * Delete the specified resource.
    //  */
    // public function destroy(Animal $animal): Response
    // {
    //     $animal->delete();

    //     return response()->noContent();
    // }
}

    public function push(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
            'data.*.uid' => 'required|string',
            'data.*.version' => 'required|integer',
        ]);

        $applied = [];
        $conflicts = [];
        $errors = [];

        foreach ($request->input('data', []) as $item) {
            $uid = $item['uid'] ?? null;
            try {
                $validator = Validator::make($item, [
                    'uid' => 'required|string',
                    'version' => 'required|integer',
                    'species' => 'nullable|string',
                    'sex' => 'nullable|string',
                    'birth_date' => 'nullable|date',
                    'life_status' => 'nullable|string',
                    'premise_uid' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    $errors[] = ['uid' => $uid, 'code' => 'VALIDATION_ERROR', 'message' => $validator->errors()->first()];
                    continue;
                }

                DB::beginTransaction();
                $existing = Animal::where('uid', $uid)->first();

                if (!$existing) {
                    $data = $validator->validated();
                    if (!empty($data['premise_uid'])) {
                        $prem = Premise::where('uid', $data['premise_uid'])->first();
                        if ($prem) $data['premises_id'] = $prem->id;
                        unset($data['premise_uid']);
                    }
                    $data['created_by'] = auth()->id();
                    Animal::create($data + ['version' => $item['version']]);
                    $applied[] = $uid;
                    DB::commit();
                    continue;
                }

                $serverVersion = (int) ($existing->version ?? 0);
                $clientVersion = (int) $item['version'];
                if ($clientVersion <= $serverVersion) {
                    $conflicts[] = ['uid' => $uid, 'server_data' => (new AnimalResource($existing))->response()->getData(true)];
                    DB::rollBack();
                    continue;
                }

                $data = $validator->validated();
                if (!empty($data['premise_uid'])) {
                    $prem = Premise::where('uid', $data['premise_uid'])->first();
                    if ($prem) $data['premises_id'] = $prem->id;
                    unset($data['premise_uid']);
                }
                $existing->fill(array_merge($data, ['version' => $clientVersion]));
                $existing->save();
                $applied[] = $uid;
                DB::commit();
            } catch (QueryException $qe) {
                DB::rollBack();
                $errors[] = ['uid' => $uid, 'code' => 'UNIQUE_CONSTRAINT', 'message' => $qe->getMessage()];
            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = ['uid' => $uid, 'code' => 'UNKNOWN_ERROR', 'message' => $e->getMessage()];
            }
        }

        return response()->json(['statut' => 'OK', 'applied' => $applied, 'conflicts' => $conflicts, 'errors' => $errors]);
    }
