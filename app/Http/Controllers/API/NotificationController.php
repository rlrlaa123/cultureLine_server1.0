<?php

namespace App\Http\Controllers\API;

use App\Notification;
use App\TestUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function postToken(Request $request)
    {
        $user = TestUser::where('token', $request->token)->first();

        if ($user === null) {
            $user = new TestUser;

            $user->token = $request->token;

            $user->save();
        }
        else {
            $user->token = $request->token;

            $user->save();
        }
    }
}
