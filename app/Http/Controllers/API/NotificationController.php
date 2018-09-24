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

    public function sendNotification()
    {
        $tokenList = TestUser::all();

        $tokens = [];

        for ($i = 0; $i < count($tokenList); $i++) {
            array_push($tokens, $tokenList[$i]->token);
        }

        $message = array("message" => "FCM PUSH NOTIFICATION");

        return $this->sendToFirebase($tokens, $message);
    }

    public function sendToFirebase($tokens, $message)
    {
        $client = new \GuzzleHttp\Client;

        $result = $client->request('POST', 'http://fcm.googleapis.com/fcm/send',
            [
                'headers' => [
                    'Authorization' => 'AAAAYarihTU:APA91bGbZ0OBbQDKFFoP1EZ9_Xr6vubkyDjfXJSGj_A5kgXAa5K4Za4aPvM5HhqOoTjzZehmEl58udqEGdH-DJ7m5USJgcjloi4RB8U3Lx0WW4F11S-X3S3HYJ7aav1D2DATb7BdKtNg',
                    'Content-Type' => 'application/json',
                ],
                'form_params' => [
                    'registration_ids' => $tokens,
                    'data' => $message
                ]
            ]
        );

        return $result;
    }
}
