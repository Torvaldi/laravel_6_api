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


// Auth route without auth
Route::post('/api/register', 'AuthController@register');
Route::post('/api/login', 'AuthController@login');

// Auth route with auth

Route::group(['middleware' => ['token']], function(){

    Route::post('/api/password', 'AuthController@editPassword');
    
    Route::post('/api/game.create', 'LobbyController@createGame');

    Route::post('/api/game.join', 'LobbyController@gameJoin');

    Route::get('/api/game.status', 'LobbyController@getGamesByStatus');

    Route::get('/api/game.user.running', 'LobbyController@getUserRunningGame');

    
    Route::get('/api/game.user', 'GameController@getGamePlayers');

    Route::put('/api/game.status', 'GameController@updateGameStatus');

    //Route::delete('/api/game.user.leave', 'GameController@userLeaveGame'); dead route

    Route::get('/api/anime.index', 'AnimeController@index');

    Route::put('/api/game.user.save', 'GameController@userSaveScore');
});
