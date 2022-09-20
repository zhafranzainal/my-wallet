<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->return_api(false, Response::HTTP_UNPROCESSABLE_ENTITY, null, null, $validator->errors());
        }

        if (Auth::attempt($validator->validated(), true)) {
            $user = Auth::user();
            $user->access_token =  $user->createToken('authToken')->plainTextToken;
            return $this->return_api(true, Response::HTTP_OK, null, new UserResource($user), null);
        } else {
            return $this->return_api(false, Response::HTTP_UNAUTHORIZED, trans("auth.failed"), null, null);
        }
    }

    public function logout()
    {

        // Revoke all tokens...
        $user = Auth::user()->tokens()->delete();

        return $this->return_api(true, Response::HTTP_OK, null, null, null);
    }
}
