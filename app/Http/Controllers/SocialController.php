<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function redirectToProvider(Request $request, $provider)
    {
        $user = User::where('provider_user_id', $request->token)->first();

        if($user) {
            $credentials = [$request->email, Hash::make('secret')];

            if (! $token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => auth()->user(),
            ]);
        }
        else {
            $user = new User;
            $user->email = $request->email;
            $user->password = Hash::make('social');
            $user->save();

            $credentials = [$request->email, Hash::make('secret')];

            $token = auth()->attempt($credentials);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => null,
            ]);
        }
    }

    protected function respondWithToken($token)
    {

    }
}
