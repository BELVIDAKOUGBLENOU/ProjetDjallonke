<?php

namespace App\Http\Controllers\Api\Syncing;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class UserSyncController extends Controller
{
    //
    function userInfo(Request $request)
    {
        $data = $request->header("X-Origin", null);
        if ($data && $data == "Vue-Djallonke") {
            $fromRemote = true;
        } else {
            $fromRemote = false;
        }
        // $fromRemote = true;
        return UserResource::make($request->user())->setToRemote($fromRemote);
    }
}
