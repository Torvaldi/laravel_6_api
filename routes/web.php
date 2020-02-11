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

    Route::post('/api/password', 'AuthController@editPassword');
    
    // /api/game.store ok
    Route::post('/api/game.create', 'LobbyController@createGame');
    // /api/store.join ok
    Route::post('/api/game.join', 'LobbyController@gameJoin');
    // /api/game.index.status ok
    Route::get('/api/game.status', 'LobbyController@getGamesByStatus');
    // /api/game.index.running ok
    Route::get('/api/game.user.running', 'LobbyController@getUserRunningGame');

    // GAME
    // delete /api/game.show

    // /api/game.user.show ok
    Route::get('/api/game.user', 'GameController@getGamePlayers');
    // post ok
    Route::put('/api/game.status', 'GameController@updateGameStatus');
    // post
    Route::delete('/api/game.user.leave', 'GameController@userLeaveGame');

    //Anime route
    Route::get('/api/anime.index', 'AnimeController@index');
});
