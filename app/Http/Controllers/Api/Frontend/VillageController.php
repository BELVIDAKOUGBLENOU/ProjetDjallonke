<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Models\Village;
use App\Http\Common\Constant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;

class VillageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Simple list for dropdowns
        $villages = Village::orderBy('name')->get();
        return JsonResource::collection($villages);
    }
}
