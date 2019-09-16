<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Auth route without auth
Route::post('/api/register', 'AuthController@register');
Route::post('/api/login', 'AuthController@login');

// Auth route with auth
Route::group(['middleware' => ['token']], function(){

    Route::post('/api/password/{name}', 'AuthController@editPassword');
    
    //Game route
    Route::post('/game.store', 'GameController@store');
    Route::post('/game.store.join', 'GameController@storeUserGame');
    Route::get('/game.index.status', 'GameController@indexGameStatus');
    Route::get('/game.index.running', 'GameController@indexGameRunning');
    Route::get('/game.show', 'GameController@getGame');
    Route::get('/game.user.show', 'GameController@getUserByGame');
    Route::post('/game.status', 'GameController@changeGameStatus');
    Route::post('/game.user.leave', 'GameController@userLeaveGame');

    //Anime route
    Route::get('/anime.index', 'AnimeController@index');
});
