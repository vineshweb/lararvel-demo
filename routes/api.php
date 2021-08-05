<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//Route::get('/register', [App\Http\Controllers\Api\LoginController::class, 'signup'])->name('signup');
Route::post('/register/{email}', 'Api\LoginController@signup');
Route::post('/verify-pin', 'Api\LoginController@verifyPin');
Route::post('/login', 'Api\LoginController@login');
Route::post('/update-profile', 'Api\LoginController@updateProfile');
