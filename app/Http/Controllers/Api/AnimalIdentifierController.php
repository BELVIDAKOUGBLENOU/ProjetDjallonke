<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnimalIdentifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Models\Animal;

class AnimalIdentifierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $communityId = getPermissionsTeamId();
        $since = $request->validate([
            'since' => 'nullable|date_format:Y-m-d H:i:s',
        ])['since'] ?? "1970-01-01 00:00:00";

        $identifiers = AnimalIdentifier::whereHas('animal', function ($q) use ($communityId) {
            $q->whereHas('premise', function ($q2) use ($communityId) {
                $q2->where('community_id', $communityId);
            });
        })->when($since, function ($query, $since) {
            $query->where('updated_at', '>=', $since);
        })->paginate();

        $result = $identifiers->toArray();
        if ($identifiers->currentPage() >= $identifiers->lastPage()) {
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
                    'animal_uid' => 'required|string',
                    'type' => 'nullable|string',
                    'code' => 'nullable|string',
                    'active' => 'nullable|boolean',
                ]);

                if ($validator->fails()) {
                    $errors[] = ['uid' => $uid, 'code' => 'VALIDATION_ERROR', 'message' => $validator->errors()->first()];
                    continue;
                }

                DB::beginTransaction();
                $existing = AnimalIdentifier::where('uid', $uid)->first();
                $animal = Animal::where('uid', $item['animal_uid'])->first();
                if (!$animal) {
                    DB::rollBack();
                    $errors[] = ['uid' => $uid, 'code' => 'MISSING_RELATION', 'message' => 'Animal not found'];
                    continue;
                }

                if (!$existing) {
                    $data = $validator->validated();
                    $data['animal_id'] = $animal->id;
                    AnimalIdentifier::create(array_merge($data, ['version' => $item['version']]));
                    $applied[] = $uid;
                    DB::commit();
                    continue;
                }

                $serverVersion = (int) ($existing->version ?? 0);
                $clientVersion = (int) $item['version'];
                if ($clientVersion <= $serverVersion) {
                    $conflicts[] = ['uid' => $uid, 'server_data' => $existing->toArray()];
                    DB::rollBack();
                    continue;
                }

                $existing->fill([
                    'animal_id' => $animal->id,
                    'type' => $item['type'] ?? $existing->type,
                    'code' => $item['code'] ?? $existing->code,
                    'active' => $item['active'] ?? $existing->active,
                    'version' => $clientVersion,
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
