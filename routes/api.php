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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'user', 'namespace' => 'Api'], function () {

    Route::post('register', 'AuthController@register');
    Route::post('verify', 'AuthController@userVerify');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');

    Route::group(['middleware' => 'auth:api'], function () {

        Route::get('profile', 'AuthController@profile');

    });

});
