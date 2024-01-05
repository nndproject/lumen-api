<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth:api', ['except' => ['login', 'refresh', 'logout']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {

        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        $user   = User::where('email', $request->input('email'))->first();

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'UH! Unauthorized'], 401);
        }

       /*  $user = User::where('email', $request->email)->first();
        if(!$user) {
            return response()->json(['message' => 'Unauthorized'],401);
        }
        $isValidPassword = Hash::check($request->password, $user->password);
        if(!$isValidPassword) {
            return response()->json(['message' => 'Unauthorized'],401);
        }

        $generateToken = bin2hex(random_bytes(40));
        $user->update([
            'api_token' => $generateToken,
            'remember_token' => $generateToken,
        ]); */
        /* if(empty($user->api_token) || $user->api_token == ''){
            $user->update([
                'api_token' => $token,
            ]);
        } */

        return $this->respondWithToken($token);
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
        /* if(!empty(auth()->user())){
            try {
                $newToken = auth()->refresh();
                User::where('id', auth()->user()->id)->update([
                    'api_token' => $newToken
                ]);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        return $this->respondWithToken($newToken); */
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
        $auth = auth()->user();
        $dataAuth['id']= $auth->id;
        $dataAuth['name'] = $auth->name;
        $dataAuth['email'] = $auth->email;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $dataAuth,
            'expires_in' => auth()->factory()->getTTL() * 60 * 24
        ]);
    }
}
