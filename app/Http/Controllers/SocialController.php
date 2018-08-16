<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
    public function __construct( ) {
        $this->middleware('jwt.auth', ['except' => ['socialLogin']]);
    }

    public function socialLogin(Request $request, $provider)
    {
        $user = User::where('provider_user_id', $request->token)->first();

        if ($user) {
            $credentials['email'] = $request->email;
            $credentials['password'] = 'social';
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if ($user->name == null || $user->stu_id || $user->major ) {
                return response()->json([
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60,
                    'user' => null,
                ]);
            }
            else {
                return response()->json([
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth()->factory()->getTTL() * 60,
                    'user' => auth()->user(),
                ]);
            }
        } else {
            $user = new User;
            $user->email = $request->email;
            $user->password = Hash::make('social');
            $user->provider_user_id = $request->token;

            $user->save();

            $credentials['email'] = $request->email;
            $credentials['password'] = 'social';

            $token = auth()->attempt($credentials);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => null,
            ]);
        }
    }

    protected function socialRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'stu_id' => 'required',
            'major' => 'required',
        ]);

        $validator->after(function () {
        });

        if ($validator->fails()) {
            return response($validator->errors());
        }

        $user = auth()->user();

        $user->name = $request->name;
        $user->stu_id = $request->stu_id;
        $user->major = $request->major;

        $user->save();

        return response(auth()->user(), 200);
    }
}
