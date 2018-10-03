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
        $receiver = User::where('email', $request->email)->first();
        // get Device Token
        $deviceToken = $receiver->device_token;
        $message = array("message" => $request->message);

        $result = $this->sendToFirebase($deviceToken, $message);

        if (json_decode($result, true)["success"] == "1") {
            $notification = new \App\Notification;

            $notification->sender_id = auth()->user()->id;
            $notification->receiver_id = $receiver->id;
            $notification->message = $request->message;

            $notification->save();

            return response('success', 200);
        }
        else {
            return $result;
        }
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

    public function sendToFirebase($token, $message)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            'to' => $token,
            'data' => $message
        );

        // Firebase Server Key
        $headers = array(
            'Authorization: key=AAAANieYVLo:APA91bEa4c8h0C2S5rzC3OPDooBVE8NMDGKAD451VdcsjcufiIqOjed9XbatLy85L4iThYGo_VeRzn5cAnYOCTQZ3i9DZ2fYEVCIBm3uvmh_qwxBPpnPaZUuOZfw5Zy4fzNlFPLJbDC6',
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
}
