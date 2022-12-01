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

Route::group(['middleware' => ['jwt.verify', 'role:admin'], 'prefix' => 'admin'], function() {
    // Route::get('delete-data/{id}', 'App\Http\Controllers\Api\Post\InfoController@deleteData');

    Route::group(['prefix' => 'data'], function() {
        Route::get('delete/{id}', 'App\Http\Controllers\Api\Post\InfoController@deleteData');
    });

});

Route::group(['prefix' => 'post'], function() {
    Route::middleware('jwt.verify')->post('upload', 'App\Http\Controllers\Api\Post\InfoController@upload');
    Route::middleware('jwt.verify')->get('my-data', 'App\Http\Controllers\Api\Post\InfoController@getMyData');
    Route::get('data', 'App\Http\Controllers\Api\Post\InfoController@getData');
});

Route::group(['middleware' => ['jwt.verify'], 'prefix' => 'profile'], function() {
    Route::put('upload-picture', 'App\Http\Controllers\Api\Auth\LoginController@uploadPicture');
    Route::get('user-profile', 'App\Http\Controllers\Api\Auth\LoginController@userProfile');
    Route::put('send-mail', 'App\Http\Controllers\Api\Auth\VerifyController@sendMail');
    Route::put('reset-pass', 'App\Http\Controllers\Api\Auth\ResetController@reset');
    Route::put('verify', 'App\Http\Controllers\Api\Auth\VerifyController@verify');
});

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', 'App\Http\Controllers\Api\Auth\LoginController@login');
    Route::post('register', 'App\Http\Controllers\Api\Auth\LoginController@register');
    Route::post('logout', 'App\Http\Controllers\Api\Auth\LoginController@logout');
    Route::post('refresh', 'App\Http\Controllers\Api\Auth\LoginController@refresh');
});

Route::group(['prefix' => 'job'], function() {
    Route::put('upload', 'App\Http\Controllers\Api\Job\JobController@upload');
});