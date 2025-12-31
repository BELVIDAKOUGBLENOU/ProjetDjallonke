<?php

namespace App\Http\Controllers\Api;

use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRequest;
use App\Http\Resources\CountryResource;
use App\Http\Middleware\SetCommunityContext;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Middleware\SetCommunityContextAPI;
use Illuminate\Routing\Controllers\HasMiddleware;

class CountryController extends Controller
{
    // public static function middleware(): array
    // {
    //     $table = Country::getTableName();

    //     return [
    //         'auth:sanctum',
    //         new Middleware("permission:list $table", only: ['index', 'getAllData']),
    //         new Middleware("permission:view $table", only: ['show']),
    //         new Middleware("permission:create $table", only: ['create', 'store']),
    //         new Middleware("permission:update $table", only: ['edit', 'update']),
    //         new Middleware("permission:delete $table", only: ['destroy']),
    //     ];
    // }
    public function __construct()
    {
        // Middleware pour authentification
        // $this->middleware('auth');
        // $this->middleware(SetCommunityContextAPI::class);

        // // Middleware pour permissions CRUD
        // $table = Country::getTableName();
        // $this->middleware("permission:list $table")->only('index');

    }

    // public function getAllData(Request $request): JsonResponse
    // {
    //     $countries = Country::where("is_active", true);
    //     $imbriqued = $request->boolean('imbriqued');
    //     if ($imbriqued) {
    //         $countries = $countries->with('districts.subDistricts.villages');
    //     }
    //     $countries = $countries->get();

    //     $resource = CountryResource::collection($countries);
    //     $resource->each(fn($r) => $r->setImbriqued($imbriqued));

    //     return response()->json($resource);
    // }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $imbriqued = $request->boolean('imbriqued', true);

        $validated = $request->validate([
            'cursor.updated_at' => 'nullable|date_format:Y-m-d H:i:s',
            'cursor.uid' => 'nullable',
            'limit' => 'nullable|integer|min:1|max:200',
        ]);

        $limit = $validated['limit'] ?? 100;
        $cursorUpdatedAt = $validated['cursor']['updated_at'] ?? null;
        $cursorUid = $validated['cursor']['uid'] ?? null;
        $cursorId = $cursorUid !== null ? (int) $cursorUid : null;

        $query = Country::query()
            ->where('is_active', true)
            ->orderBy('updated_at')
            ->orderBy('id');

        if ($cursorUpdatedAt && $cursorId) {
            $query->where(function ($q) use ($cursorUpdatedAt, $cursorId) {
                $q->where('updated_at', '>', $cursorUpdatedAt)
                    ->orWhere(function ($q2) use ($cursorUpdatedAt, $cursorId) {
                        $q2->where('updated_at', $cursorUpdatedAt)
                            ->where('id', '>', $cursorId);
                    });
            });
        }

        if ($imbriqued) {
            $query->with('districts.subDistricts.villages');
        }

        $items = $query->limit($limit + 1)->get();

        $hasMore = $items->count() > $limit;
        $items = $items->take($limit);

        $nextCursor = null;
        if ($items->isNotEmpty()) {
            $last = $items->last();
            $nextCursor = [
                'updated_at' => $last->updated_at->toDateTimeString(),
                'uid' => (string) $last->id,
            ];
        }

        $resource = CountryResource::collection($items);
        $resource->each(fn($r) => $r->setImbriqued($imbriqued));

        return response()->json([
            'data' => $resource,
            'cursor' => $nextCursor,
            'has_more' => $hasMore,
            'server_time' => now()->toDateTimeString(),
        ]);
    }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(CountryRequest $request): JsonResponse
    // {
    //     $country = Country::create($request->validated());

    //     return response()->json(new CountryResource($country));
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(Country $country): JsonResponse
    // {
    //     return response()->json(new CountryResource($country));
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(CountryRequest $request, Country $country): JsonResponse
    // {
    //     $country->update($request->validated());

    //     return response()->json(new CountryResource($country));
    // }

    // /**
    //  * Delete the specified resource.
    //  */
    // public function destroy(Country $country): Response
    // {
    //     try {
    //         $country->delete();

    //         return response()->noContent();
    //     } catch (\Exception $e) {
    //         return response('Impossible de supprimer ce pays', 500);
    //     }

    // }
}
