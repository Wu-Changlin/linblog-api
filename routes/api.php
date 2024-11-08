<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use \App\Http\Controllers\Api\UserController;

use \App\Http\Controllers\Api\V1\Backend\UserController;
use \App\Http\Controllers\Api\V1\Backend\LoginController;



// use App\Http\Controllers\Api\v1\backend\UserController;

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


Route::apiResource('users', UserController::class);
Route::apiResource('login', LoginController::class);
// http://localhost:9090/api/users

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
