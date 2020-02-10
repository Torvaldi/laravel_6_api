<?php

namespace App\Repositories;

use App\Game;
use App\User;
use Illuminate\Http\Request;
use App\GameUser;

class GameRepository {

    public function isUserInGame($userId) : bool
    {
        $totalUserCurrentgames = Game::select('id')->where('user_creator_id', $userId)->where('status', '!=', 3)->count();

        if($totalUserCurrentgames > 0){
            return true;
        }
        return false;
    }

    public function createGame(int $creatorId, Request $request) : Game
    {
        $game = new Game();
        $game->user_creator_id = $creatorId;
        $game->status = 1;
        $game->level = $request->input('level');
        $game->answer = $request->input('answer');
        $game->score_to_win = $request->input('score_to_win');
        $game->save(); // save to the main game table

        $game->user()->save(User::find($creatorId)); // save to the relationship table

        return $game;
    }

    public function addUserToGame(int $userId, int $gameId) : GameUser
    {
        $gameUser = new GameUser();
        $gameUser->game_id = $gameId;
        $gameUser->user_id = $userId;
        $gameUser->score = 0;
        $gameUser->save();

        return $gameUser;
    }

    public function isGameJoinable($gameId) : bool
    {
        $game = Game::find($gameId);
        if($game === null){
            return false;
        }

        $gameJoinable = Game::find($gameId)->where('status', '!=', 3);

        if($gameJoinable === null){
            return false;
        }

        return true;
    }

}