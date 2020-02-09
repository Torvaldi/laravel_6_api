<?php

namespace App\Repositories;

use App\Game;
use App\User;
use Illuminate\Http\Request;

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

}