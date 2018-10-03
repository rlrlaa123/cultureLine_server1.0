<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Kreait\Firebase\ServiceAccount;
use Laravel\Socialite\Facades\Socialite;
use Validator;

class SocialController extends Controller
{
    public function __construct( ) {
        $this->middleware('jwt.auth', ['except' => ['socialLogin']]);
    }

    public function socialLogin(Request $request, $provider)
    {
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/firebase-admin-sdk.json');

        $firebase = (new \Kreait\Firebase\Factory())
            ->withServiceAccount($serviceAccount)
            ->create();

        $idTokenString = $request->token;

        try {
            $verifiedIdToken = $firebase->getAuth()->verifyIdToken($idTokenString);

            if ($verifiedIdToken) {
                $user = User::where([
                    ['email', '=', $request->email],
                    ['sns', '=', 1],
                ])->first();

                // 1. sns 정상 로그인,
                // 2. sns 로그인으로 가입이후 추가정보를 입력하지 않았을 경우,
                if ($user) {
                    $credentials['email'] = $request->email;
                    $credentials['password'] = 'social' . $provider;
                    if (!$token = auth()->attempt($credentials)) {
                        return response()->json(['error' => 'Unauthorized'], 401);
                    }
                    // 추가정보를 입력하지 않았을 경우,
                    if ($user->name == null || $user->stu_id == null || $user->major == null ) {

                        // firebase refresh
                        $user->device_token = $request->device_token;

                        $user->save();

                        return response()->json([
                            'access_token' => $token,
                            'token_type' => 'bearer',
                            'expires_in' => auth()->factory()->getTTL() * 60,
                            'user' => null,
                        ]);
                    }
                    // sns 정상 로그인
                    else {
                        // firebase refresh
                        $user->device_token = $request->device_token;

                        $user->save();

                        return response()->json([
                            'access_token' => $token,
                            'token_type' => 'bearer',
                            'expires_in' => auth()->factory()->getTTL() * 60,
                            'user' => auth()->user(),
                        ]);
                    }
                }
                // 3. sns 회원가입,
                // 4. sns 로그인을 시도한 메일과 같은 이메일이 있을 경우,
                else {
                    $user = User::where('email', $request->email)->first();

                    // 이미 다른 방식으로 가입된 메일일 경우,
                    if ($user) {
                        return response()->json([
                            'access_token' => null,
                            'token_type' => 'bearer',
                            'expires_in' => null,
                            'user' => null,
                        ]);
                    }
                    // 새로운 유저
                    else {
                        $user = new User;
                        $user->email = $request->email;
                        $user->password = Hash::make('social' . $provider);
                        $user->sns = 1;
                        $user->provider = $provider;
                        // firebase refresh
                        $user->device_token = $request->device_token;

                        $user->save();

                        $credentials['email'] = $request->email;
                        $credentials['password'] = 'social' . $provider;

                        $token = auth()->attempt($credentials);

                        return response()->json([
                            'access_token' => $token,
                            'token_type' => 'bearer',
                            'expires_in' => auth()->factory()->getTTL() * 60,
                            'user' => null,
                        ]);
                    }
                }
            }
        } catch (\Firebase\Auth\Token\Exception\InvalidToken $e) {
            return $e . "Error";
//            echo $e->getMessage();
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
