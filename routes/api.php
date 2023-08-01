<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('v1/signup', [UserController::class, 'signUp']);
Route::post('v1/login', [UserController::class, 'login']);
Route::post('v1/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('v1/password/reset', [UserController::class, 'resetPassword']);
Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::post('v1/logout', [AuthController::class, 'logout']);
});