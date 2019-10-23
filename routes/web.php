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
/* Original_Name
* Create the game
* Route::post('/api/game.store', 'LobbyController@createGame')*/
    Route::post('/api/game.create', 'LobbyController@createGame');
    
    /* Original_Name
    * For join a game
    * Route::post('/api/store.join', 'LobbyController@gameJoin')*/
    Route::post('/api/game.join', 'LobbyController@gameJoin');

    /* Original_Name
    * For join a game
    * Route::post('/api/game.index.status', 'LobbyController@indexGameStatus')*/
    Route::get('/api/game.index.status', 'LobbyController@indexGameStatus');

    Route::get('/api/game.index.running', 'LobbyController@indexGameRunning');
    Route::get('/api/game.show', 'GameController@getGame');
    Route::get('/api/game.user.show', 'GameController@getUserByGame');
    Route::post('/api/game.status', 'GameController@changeGameStatus');
    Route::post('/api/game.user.leave', 'GameController@userLeaveGame');

    //Anime route
    Route::get('/api/anime.index', 'AnimeController@index');
});
