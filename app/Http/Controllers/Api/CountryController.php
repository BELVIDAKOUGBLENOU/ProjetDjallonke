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

        $imbriqued = true;
        $since = $request->validate([
            'since' => 'nullable|date_format:Y-m-d H:i:s',
        ])['since'] ?? "1970-01-01 00:00:00";
        $countries = Country::where("is_active", true)
            ->when($since, function ($countries) use ($since) {
                $countries = $countries->where(function ($query) use ($since) {
                    $query->where('created_at', '>=', $since)
                        ->orWhere('updated_at', '>=', $since)
                        ->orWhereHas('districts', function ($q) use ($since) {
                            $q->where('created_at', '>=', $since)
                                ->orWhere('updated_at', '>=', $since)
                                ->orWhereHas('subDistricts', function ($q2) use ($since) {
                                    $q2->where('created_at', '>=', $since)
                                        ->orWhere('updated_at', '>=', $since)
                                        ->orWhereHas('villages', function ($q3) use ($since) {
                                            $q3->where('created_at', '>=', $since)
                                                ->orWhere('updated_at', '>=', $since);
                                        });
                                });
                        });

                });
            })->when($imbriqued, function ($countries) {
                $countries = $countries->with('districts.subDistricts.villages');
            })->paginate(5);


        $resource = CountryResource::collection($countries);
        $resource->each(fn($r) => $r->setImbriqued($imbriqued));

        $result = $resource->response()->getData(true);
        // si on est à la derniere page , on ajoute les last_synced_at
        if ($countries->currentPage() >= $countries->lastPage()) {
            $result['last_synced_at'] = now()->toDateTimeString();
        }

        return response()->json($result);
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
