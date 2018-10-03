<?php

namespace App\Http\Controllers;

use App\User;
use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Request\CreateUser;
use Kreait\Firebase\ServiceAccount;
use Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        //    return $request;
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/firebase-admin-sdk.json');

        $firebase = (new Factory())
            ->withServiceAccount($serviceAccount)
            ->create();

        $idTokenString = $request->token;

        try {
            $verifiedIdToken = $firebase->getAuth()->verifyIdToken($idTokenString);

            if ($verifiedIdToken) {
                $credentials = request(['email', 'password']);

                if (! $token = auth()->attempt($credentials)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }

                return $this->respondWithToken($token);
            }
        } catch (InvalidToken $e) {
            return response($e->getMessage() . "Error", 200);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ]);
    }

    // Email Register
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed',
            'name' => 'required',
            'stu_id' => 'required',
            'major' => 'required',
//            'device_token' => 'required',
        ]);

        $validator->after(function () {
        });

        if ($validator->fails()) {
            return response($validator->errors());
        }

        // Server Create User
        $user = new User;

        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->name = $request->name;
        $user->stu_id = $request->stu_id;
        $user->major = $request->major;
//        $user->device_token = $request->device_token;

        $user->save();

        // Firebase Server Create User
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/firebase-admin-sdk.json');

        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->create();

        $auth = $firebase->getAuth();

        $userProperties = [
            'email' => $request->email,
            'password' => $request->password,
            'displayName' => $request->name,
        ];
        try {
            $createdUser = $auth->createUser($userProperties);

            return response('success', 200);
        } catch(\Exception $e) {
            return $e;
        }

    }

    public function testLogin(Request $request)
    {

    }
}
