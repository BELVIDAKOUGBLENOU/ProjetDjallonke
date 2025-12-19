<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeographicManagementController extends Controller
{
    public function index()
    {
        return view('geographic-management.index');
    }
}
