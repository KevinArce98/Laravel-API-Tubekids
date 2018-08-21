<?php

use Illuminate\Http\Request;

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

// Auth Routes
Route::post('login', 'AuthController@login');
Route::get('auth/user', 'AuthController@user');
Route::post('/logout', 'AuthController@logout');
Route::get('/token/refresh', 'AuthController@refresh');

Route::resource('users', 'UsersController', ['except' => ['create','edit']]);
Route::resource('users.videos', 'UserVideoController')->only('index');
Route::resource('users.kids', 'UserKidController')->only('index');
Route::get('users/verify/{token}', 'UsersController@verify')->name('users.verify');
Route::get('users/{user}/resend', 'UsersController@resend')->name('users.resend');

Route::resource('videos', 'VideosController', ['except' => ['create','edit']]);
Route::resource('videos.users', 'VideoUserController')->only('index');

Route::resource('kids', 'KidsController', ['except' => ['create','edit']]);
Route::resource('kids.users', 'KidUserController')->only('index');

//Route::resource('Users', 'UsersController', ['only' => ['index','show']]);
//Route::resource('Users', 'UsersController', ['except' => ['update','edit']]);

/**
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

