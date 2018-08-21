<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Kid;
use App\User;
Use \Config;
use Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends ApiController
{
    /**
     * Instantiate a new AuthController instance.
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', ['only' => ['user', 'logout']]);
    }
    public function login(Request $request){
        $type_user = "";
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $request->email)->first();
            $type_user = "user";
        } else {
            $user = Kid::where('username', $request->email)->first();
            $type_user = "kid";
        }
        $validation = $this->validateCredentials($user, $request, $type_user)->getStatusCode();
        if ($validation != 200) {
            return $this->errorResponse('Invalid Credentials.', 400);
        }

        $customClaims = ['info' => $user, 'type_user' => $type_user];

        if ( ! $token = JWTAuth::fromUser($user, $customClaims)) {
            return $this->errorResponse('Invalid Credentials.', 400);
        }

        return $this->showMessage($token, 200);
    }

    private function validateCredentials($user, $request, $type){
        if ($user == null) {
            return response("Invalid Credentials", 400);
        }
        if ($type == 'kid') {
            $validCredentials = Hash::check($request['password'], $user->pin);
        }else{
            $validCredentials = Hash::check($request['password'], $user->password);
        }
        
        if (!$validCredentials) {
            return response("Invalid Credentials.", 400);
        }
        return response("success.", 200);
    }

    public function user(Request $request)
    {
        $payload = JWTAuth::parseToken()->getPayload();
        $type_user = $payload->get('type_user');

        if ($type_user == 'kid') {
            $user = Kid::find(Auth::user()->id);   
        }else if($type_user == 'user'){
            $user = User::find(Auth::user()->id);
        }else{
            return $this->errorResponse('Type User Invalid', 400);
        }
        return $this->showOne($user, 200);
    }

    public function logout()
    {
        JWTAuth::invalidate();
        return $this->successResponse('Logged out Successfully.', 200);
    }

    public function refresh()
    {
        return $this->successResponse('Success.', 200);
    }
}
