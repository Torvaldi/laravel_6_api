<?php

namespace App\Repositories;

use App\Game;
use App\User;
use Illuminate\Http\Request;
use App\GameUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class GameRepository {

    public function isUserInGame($userId) : bool
    {
        $sql = "SELECT game_user.user_id
        FROM games
        JOIN game_user ON game_user.game_id = games.id
        WHERE (games.status = 1 OR games.status = 2) AND game_user.user_id = ?";

        $totalUserCurrentgames = DB::select($sql, [$userId]);

        if(count($totalUserCurrentgames) > 0){
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
        $game->save();

         // save to the relationship table
        $user = User::find($creatorId);
        $game->user()->save($user, ['score' => 0]);
        

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

    public function getGameByStatus(int $statusId) : array
    {
        $sql = "SELECT 
        games.id, user_creator_id, username as creator, status, level, answer, games.created_at, games.updated_at, score_to_win, count(game_user.user_id) as total_player
        FROM games
        JOIN game_user ON game_user.game_id = games.id
        JOIN users ON users.id = games.user_creator_id
        WHERE games.status = ?
        GROUP BY games.id
        HAVING total_player < 10
        ORDER BY games.created_at DESC";

        $games = DB::select($sql, [$statusId]);

        return $games;
    }

    public function getUserRunningGame(int $userId) : ?array
    {
        // check if the user has any game in the game_user table
        $sqlGameUser = "SELECT game_id FROM game_user WHERE user_id = ?";

        $gameUser = DB::select($sqlGameUser, [$userId]);

        // if he doesin't have any game, return null
        if(count($gameUser) === 0) return null;

        // check if the user is actually part of a game that is waiting for player or running
        $sql = "SELECT games.id, username as creator, level, answer, score_to_win, games.created_at, games.updated_at, status, count(game_user.user_id) as total_player
        FROM game_user
        JOIN games ON games.id = game_user.game_id
        JOIN users ON users.id = games.user_creator_id
        WHERE games.status = 1 OR games.status = 2 AND games.id IN (
            SELECT game_id 
            FROM game_user
            WHERE user_id = ?
        )
        GROUP BY id
        ORDER BY games.created_at DESC";

        $games = DB::select($sql, [$userId]);

        // if there aren't any game return null
        if(count($games) <= 0){
            return null;
        }
        // get_object_vars, transform stdcladd to array
        return get_object_vars($games[0]); // only send back one game, the last the most recent one
    }

    public function getGameById($gameId) : ?Game
    {
        return Game::find($gameId);
    }

    public function getUserOfTheGame($gameId) : array
    {
        $sql = "SELECT users.id, username 
        FROM game_user
        JOIN users ON users.id = game_user.user_id
        WHERE game_id = ?";

        $users = DB::select($sql, [$gameId]);

        return $users; 
    }

    public function doesGameExist($gameId) : bool
    {
        $game = Game::find($gameId);

        if($game === null){
            return false;
        }

        return true;
    }

    public function updateGameStatus(int $gameId, int $status) : int
    {
        $game = Game::find($gameId);
        $game->status = $status;
        $game->save();

        return $game->status;
    }

    /**
     * @return int, return the number of row deleted
     */
    public function removeUserFromGame(int $userId, int $gameId) : int
    {
        return DB::table('game_user')->where('game_id', '=', $gameId)->where('user_id', '=', $userId)->delete();
    }

    /**
     * save users scores to the database, called at the end of a game
     * @param array $player, an array of players, contain userName, score and rank values
     * @param int, $gameId, simply the id of the game we want to save the scores to
     * @return int, the $gameId
     */
    public function saveUsersScore(array $players, int $gameId) : int
    {
        // loop throught all players to save the score of each one of them
        foreach($players as $player){

            // get user Model, userfull to retrive user'id later on
            $playerModel = User::where('username', $player['userName'])->first();
            
            // prevent from saving if the User is not found
            if($playerModel === null) continue;

            // update user score to the database
            DB::table('game_user')
            ->where('game_id', '=', $gameId)
            ->where('user_id', '=', $playerModel->getId())
            ->update(['score' => $player['score']]);
        }

        return $gameId;
    }
}