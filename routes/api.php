<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function(){

    Route::post('register',[AuthController::class,'register']);
    Route::post('login',[AuthController::class,'login']);
    
    Route::post('forgot',[ForgotController::class,'forgot']);
    Route::post('reset',[ForgotController::class,'reset']);
    
    Route::group(['middleware' => 'auth:api'],function(){
        Route::get('logout',[AuthController::class,'logout']);
    });
});

Route::prefix('user')->group(function(){
    Route::post('admin_scope',[AuthController::class,'handleAdmin'])->middleware('auth:api');

    Route::post('guest_scope',function(){
        return response()->json(['message' => "guest access"],200);
    })->middleware(['auth:api','scope:check-status,do_anything']);
});

