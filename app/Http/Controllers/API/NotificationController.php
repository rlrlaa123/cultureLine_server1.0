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
    public function sendNotification(Request $request)
    {
        // get Device Token
        $user = User::where('email', $request->email)->first();
        $deviceToken = array("Token" => $user->device_token);
        $message = array("message" => $request->body);

        return $this->sendToFirebase($deviceToken, $request->body);

        // set Title, Body
//        $title = $request->title;
//        $body = $request->body;
//
//        // create Notification
//        $notification = Notification::create($title, $body);
//
//        $message = CloudMessage::withTarget('token', $deviceToken)
//            ->withNotification($notification);
//
//        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/firebase-admin-sdk.json');
//        $firebase = (new Factory())
//            ->withServiceAccount($serviceAccount)
//            ->create();
//        $messaging = $firebase->getMessaging();
//        $messaging->send($message);

        return response('success', 200);
    }

    public function index($sender_id, $receiver_id)
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
//            ->orWhere('sender_id', $receiver_id)
//            ->orWhere('receiver_id', $sender_id)
//            ->orWhere('receiver_id', $receiver_id)
//            ->get();

        return response($notifications, 200);
    }

    public function store(Request $request)
    {
        $notification = new \App\Notification;

        $notification->sender_id = $request->sender_id;
        $notification->receiver_id = $request->receiver_id;
        $notification->message = $request->message;

        $notification->save();

        return response('success', 200);
    }

//    public function postToken(Request $request)
//    {
//        $user = TestUser::where('token', $request->token)->first();
//
//        if ($user === null) {
//            $user = new TestUser;
//
//            $user->token = $request->token;
//            $user->save();
//        }
//        else {
//            $user->token = $request->token;
//
//            $user->save();
//        }
//    }

    public function sendToFirebase($tokens, $message)
    {
        $url = 'https://fcm.googleapis.com/v1/projects/cultureline-664f1/messages:send';
        $fields = array(
            'registration_ids' => $tokens,
            'data' => $message
        );

        $headers = array(
            'Authorization: Bearer AAAANieYVLo:APA91bEa4c8h0C2S5rzC3OPDooBVE8NMDGKAD451VdcsjcufiIqOjed9XbatLy85L4iThYGo_VeRzn5cAnYOCTQZ3i9DZ2fYEVCIBm3uvmh_qwxBPpnPaZUuOZfw5Zy4fzNlFPLJbDC6',
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);

        if ($result == FALSE) {
            die('CURL failed: ' . curl_error($ch));
        }

        curl_close($ch);

        return $result;
    }
//
////        $client = new \GuzzleHttp\Client([
////            'base_uri' => '',
////        ]);
////
////        $result = $client->post('fcm/send',
////            [
////                'debug' => TRUE,
////                'headers' => [
////                    'Authorization' => 'key=',
////                    'Content-Type' => 'application/json',
////                ],
////                'form_params' => [
////                    'registration_ids' => $tokens,
////                    'data' => $message,
////                ]
////            ]
////        );
//
//        return $result;
//    }
}
