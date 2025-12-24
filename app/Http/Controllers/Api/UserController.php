<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    //
    function userInfo(Request $request)
    {
        return UserResource::make($request->user());
    }
}
