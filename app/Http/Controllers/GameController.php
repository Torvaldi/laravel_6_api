<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\JwtService;
use App\Repositories\GameRepository;
use Validator;

class GameController extends Controller
{

    public $jwt;
    public $gameRepository;

    public function __construct(JwtService $jwt, GameRepository $gameRepository)
    {
        $this->jwt = $jwt;
        $this->gameRepository = $gameRepository;
    }

    /**
     * Get all the players of a given game
     */
    public function getGamePlayers(Request $request) : Response
    {
        // validator
        $validator = Validator::make($request->all(), ['id' => 'required|numeric|']);
        if($validator->fails()){
            return response()->json(["error" => $validator->errors()->all()], 400);
        }

        // check if game id is an existing game id
        $gameId = $request->input('id');
        if($this->gameRepository->doesGameExist($gameId) === false){
            return response()->json(["error" => ['Game not found']], 400);
        }

        // return a list of users
        $userOfTheGame = $this->gameRepository->getUserOfTheGame($gameId);
        
        return response()->json($userOfTheGame, 200);
    }


    /**
     * update the game status
     * 1 : Waiting for player
     * 2: Running
     * 3: Finish
     */
    public function updateGameStatus(Request $request) : Response
    {
        // validator
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
            'status' => 'required|numeric|min:1|max:3'
        ]);

        if($validator->fails()){
            return response()->json(["error" => $validator->errors()->all()], 400);
        }
        
        // check if id is an existing game id
        $gameId = $request->input('id');
        if($this->gameRepository->doesGameExist($gameId) === false){
            return response()->json(["error" => ['Game not found']], 400);
        }

        // update game status
        $statusId = $request->input('status');
        $this->gameRepository->updateGameStatus($gameId, $statusId);

        return response()->json(['status' => $statusId], 200);
    }

    /**
     * Auth user leave an unfinised game
     */
    public function userLeaveGame(Request $request) 
    {
        // validator
        $validator = Validator::make($request->all(), [
            'game_id' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json(["error" => $validator->errors()->all()], 400);
        }

        // get user id
        $userId = $this->jwt->getAuthUserId($request);
        $gameId = $request->input('game_id');

        // delete game user link onf the pilot table
        $numberOfResultDeleted = $this->gameRepository->removeUserFromGame($userId, $gameId);
        if($numberOfResultDeleted === 0){
            return response()->json(["error" => 'This user is not in this game'], 400);
        }

        // send back the game id just removed
        return response()->json(['game_id', $gameId]);
    }
}
