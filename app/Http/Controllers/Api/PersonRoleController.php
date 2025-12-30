<?php

namespace App\Http\Controllers\Api;

use App\Models\PersonRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PersonRoleResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Models\Person;
use App\Models\Animal;

class PersonRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $communityId = getPermissionsTeamId();
        $since = $request->validate([
            'since' => 'nullable|date_format:Y-m-d H:i:s',
        ])['since'] ?? "1970-01-01 00:00:00";

        $roles = PersonRole::query()
            ->when($communityId, function ($query) use ($communityId) {
                $query->whereHas('animal', function ($q) use ($communityId) {
                    $q->whereHas('premise', function ($q) use ($communityId) {
                        $q->where('community_id', $communityId);
                    });
                });
            })
            ->when($since, function ($query) use ($since) {
                $query->where(function ($q) use ($since) {
                    $q->where('created_at', '>=', $since)
                        ->orWhere('updated_at', '>=', $since);
                });
            })
            ->paginate();

        $resource = PersonRoleResource::collection($roles);
        $result = $resource->response()->getData(true);
        if ($roles->currentPage() >= $roles->lastPage()) {
            $result['last_synced_at'] = now()->toDateTimeString();
        }

        return response()->json($result);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
                    'person_uid' => 'required|string',
                    'animal_uid' => 'required|string',
                    'role_type' => 'required|string',
                ]);

                if ($validator->fails()) {
                    $errors[] = ['uid' => $uid, 'code' => 'VALIDATION_ERROR', 'message' => $validator->errors()->first()];
                    continue;
                }

                DB::beginTransaction();
                $existing = PersonRole::where('uid', $uid)->first();
                $person = Person::where('uid', $item['person_uid'])->first();
                $animal = Animal::where('uid', $item['animal_uid'])->first();
                if (!$person || !$animal) {
                    DB::rollBack();
                    $errors[] = ['uid' => $uid, 'code' => 'MISSING_RELATION', 'message' => 'Person or Animal not found'];
                    continue;
                }

                if (!$existing) {
                    PersonRole::create([
                        'uid' => $uid,
                        'version' => $item['version'],
                        'person_id' => $person->id,
                        'animal_id' => $animal->id,
                        'role_type' => $item['role_type'],
                    ]);
                    $applied[] = $uid;
                    DB::commit();
                    continue;
                }

                $serverVersion = (int) ($existing->version ?? 0);
                $clientVersion = (int) $item['version'];
                if ($clientVersion <= $serverVersion) {
                    $conflicts[] = ['uid' => $uid, 'server_data' => (new PersonRoleResource($existing))->response()->getData(true)];
                    DB::rollBack();
                    continue;
                }

                $existing->fill([
                    'version' => $clientVersion,
                    'person_id' => $person->id,
                    'animal_id' => $animal->id,
                    'role_type' => $item['role_type'],
                ]);
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
}
