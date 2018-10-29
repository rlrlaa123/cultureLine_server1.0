<?php

namespace App\Http\Controllers\API;

use App\TestUser;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\ServiceAccount;

class NotificationController extends Controller
{
    public function __construct( ) {
        $this->middleware('jwt.auth');
    }

    public function sendNotification(Request $request)
    {
        $receiver = User::where('email', $request->email)->first();
        // get Device Token
        $deviceToken = $receiver->device_token;

        $message = array(
            "message" => $request->message,
            "sender_name" => auth()->user()->name,
        );

        $result = event('sendToFirebase', [$deviceToken, $message]);

        if (json_decode($result[0], true)["success"] == "1") {
            $notification = new \App\Notification;

            $notification->sender_id = auth()->user()->id;
            $notification->receiver_id = $receiver->id;
            $notification->message = $request->message;

            $notification->save();

            return response('success', 200);
        }
        else {
            return $result[0];
        }
    }

    public function index()
    {
        $notifications = \App\Notification::orderBy('updated_at', 'desc')
            ->where('sender_id', auth()->user()->id)
            ->orWhere('receiver_id', auth()->user()->id)
            ->get();

        $results = [];

        // initialize notification's result array
        $user_lists = [$notifications[0]->sender_id, $notifications[0]->receiver_id];
        $user_id = null;

        foreach ($user_lists as $user_list) {
            if (!($user_list == auth()->user()->id)) {
                $user_id = $user_list;
            }
        }

        $user = User::find($user_id);

        $notifications[0]['user'] = $user;

        array_push($results, $notifications[0]);

        // add notification's result in result array
        foreach ($notifications as $notification) {
            $count = 0;
            for ($i = 0; $i < count($results); $i++) {
                if (($results[$i]->sender_id == $notification->sender_id && $results[$i]->receiver_id == $notification->receiver_id)
                    || ($results[$i]->sender_id == $notification->receiver_id && $results[$i]->receiver_id == $notification->sender_id)) {
                    $count += 1;
                }
            }

            if ($count == 0) {
                $user_lists = [$notification->sender_id, $notification->receiver_id];
                $user_id = null;

                foreach ($user_lists as $user_list) {
                    if (!($user_list == auth()->user()->id)) {
                        $user_id = $user_list;
                    }
                }

                $user = User::find($user_id);

                $notification['user'] = $user;

                array_push($results, $notification);
            }
        }

        return response($results, 200);
    }

    public function show($sender_id, $receiver_id)
    {
        $notifications = \App\Notification::orderBy('created_at')
            ->where([
                ['sender_id', '=', $sender_id],
                ['receiver_id', '=', $receiver_id]
            ])
            ->orWhere([
                ['sender_id', '=', $receiver_id],
                ['receiver_id', '=', $sender_id]
            ])
            ->get();

        foreach ($notifications as $notification) {
            $sender_profile = User::find($notification->sender_id);
            $receiver_profile = User::find($notification->receiver_id);

            $notification->sender_profile = $sender_profile->profile;
            $notification->receiver_profile = $receiver_profile->profile;
        }

        return response($notifications, 200);
    }
}
