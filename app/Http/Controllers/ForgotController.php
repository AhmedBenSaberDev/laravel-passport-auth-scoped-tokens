<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;


class ForgotController extends Controller
{
    public function forgot(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'email' => 'required|email'
        ]);

        if(!$validation->fails())
        {
            $user = User::where('email',$request->email)->first();

            if(!$user){
                return response()->json(['message' => "User doese not exist's"],400);
            }

            Db::table('password_resets')->where('email',$user->email)->delete();

            $token = Str::random(30);

            DB::table("password_resets")->insert(['email' => $user->email , "token" => $token]);

            try {
               
                Mail::to($user->email)->send(new ResetPassword($user->name,$token));

                return response()->json(['message' => "Check your email"],200);    

            } catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()],400);    
            }

        }
        return response()->json(['message' => "error","validation_errors" => $validation->errors()],400);
    }
    public function reset(Request $request)
    {
    
        $validation = Validator::make($request->all(),
        ['password' => "required",
        'password_confirm' => "required|same:password",
        'token' => 'required'
        ]);

        if(!$validation->fails())
        {
            $user = Db::table('password_resets')->where('token',$request->token)->first();
            if(!$user){
                return response()->json(['message' => "Token dosen't match"],400);
            }
            $user = User::where('email',$user->email)->first();

            $user->password = bcrypt($request->password);
            $user->save();

            return response()->json(['message' => 'Password changed successfull y'],200);
        }

        return response()->json(['message' => "An error occured" , "validation_errors" => $validation->errors()],400);
    }

}
