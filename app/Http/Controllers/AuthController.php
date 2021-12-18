<?php

namespace App\Http\Controllers;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'password_confirm' => 'required|same:password'
        ]);

        if ($validation->fails()) {
            return response()->json(['messsage' => 'An error occured', 'form_validation' => $validation->errors()], 400);
        }

        $user = User::create(array_merge($request->all(), ['password' => bcrypt($request->password)]));

        return response()->json(['message' => "user created successfully", 'user' => $user], 201);
    }

    public function login(Request $request)
    {
        $validation = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ]
        );

        if (!$validation->fails()) {

            if (Auth::attempt($validation->validated())) {

                $user = Auth::user();

                if($user->role == 'administrator')
                {
                    $token = $user->createToken('auth',['do_anything']);
                }else{
                    $token = $user->createToken('auth',['check-status']);
                }
                return response()->json(
                    ['message' => 'success',
                    'user' => $user,
                    'token' => $token->accessToken,
                    'token_scopes' => $token->token->scopes[0]
                    ], 
                    200);
            }else{
                return response()->json(['message' => 'Invalid Username or Password'], 400);
            }
        }

        return response()->json(['message' => 'error','validation_errors'=> $validation->errors()], 400);
    }

    public function logout(Request $request) {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'User successfully signed out'],200);
    }


    public function handleAdmin(Request $request)
    {
        if($request->user()->tokenCan('do_anything')  ){
            return response()->json(['message' => "admin access"],200);
        }
        return response()->json(['message' => "unauthorized"],400);
    }

    public function handleGuest(Request $request)
    {
        if($request->user()->tokenCan('do_anything') || $request->user()->tokenCan('check-status')){
            return response()->json(['message' => "guest access"],200);
        }
        return response()->json(['message' => "unauthorized"],400);
    }
}