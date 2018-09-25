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

        $tokens = array();

        if (count($tokenList) > 0 ) {
            foreach ($tokenList as $token) {
                array_push($tokens, $token->token);
            }
        }

        $message = array("message" => "FCM PUSH NOTIFICATION");

        return $this->sendToFirebase($tokens, $message);
    }

    public function sendToFirebase($tokens, $message)
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://fcm.googleapis.com',
        ]);

        $result = $client->post('fcm/send',
            [
                'debug' => TRUE,
                'headers' => [
                    'Authorization' => 'key=AAAAYarihTU:APA91bHRDdpz_7pUe6cCjVw_-Kpq3gyNoV_sncd41TtLecBp23oYXlS35udmbiFuDoS1VTdWPEXb9WIzS7j4CanlWSf1m_dpyCRdu-fMHzLwsUElb4ohEwfmiS4gYnurLVXhDN33srRX',
                    'Content-Type' => 'application/json',
                ],
                'form_params' => [
                    'registration_ids' => $tokens,
                    'data' => $message,
                ]
            ]
        );


        return $result->getBody();
    }
}
