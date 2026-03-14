<?php

namespace App\Http\Controllers\Api\Syncing;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserSyncController extends Controller
{
    //
    public function userInfo(Request $request)
    {
        $data = $request->header('X-Origin', null);
        if ($data && $data == 'Vue-Djallonke') {
            $fromRemote = true;
        } else {
            $fromRemote = false;
        }
        // dd($request->user());
        // Log::info('User info requested', ['user_id' => $request->user()->id, 'from_remote' => $fromRemote]);

        // $fromRemote = true;
        return UserResource::make($request->user())->setToRemote($fromRemote);
    }
}
