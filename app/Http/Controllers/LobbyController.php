<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JwtService;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\GameRepository;
use Validator;

class LobbyController extends Controller {

    public $jwt;
    public $gameRepository;

    public function __construct(JwtService $jwt, GameRepository $gameRepository)
    {
        $this->jwt = $jwt;
        $this->gameRepository = $gameRepository;
    }
    
    /**
     * Auth user create a game
     */
    public function createGame(Request $request): Response
    {
        // get auth user
        $creatorId = $this->jwt->getAuthUserId($request);

        // check if user is not already in a game
        if($this->gameRepository->isUserInGame($creatorId) === true){
            return response()->json(['error' => ['Your are already in a game']], 400);
        }
        
        // validate request
        $rules = [
            'level' => 'required|numeric|min:1|max:3',
            'answer' => 'required|numeric|min:5|max:15',
            'score_to_win' => 'required|numeric|min:10|max:500',
            'musicType' => 'required|numeric|min:0|max:2'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json(["error" => $validator->errors()->all()], 400);
        }

        // create a new game
        $game = $this->gameRepository->createGame($creatorId, $request);

        return response()->json($game->getGame(), 200);
    }

    /**
     * Auth user join a game
     */
    public function gameJoin(Request $request) : Response
    {
        // Validator
        $rules = ['game_id' => 'required|numeric'];
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json(["error" => $validator->errors()->all()], 400);
        }

        // get auth user and game
        $userId = $this->jwt->getAuthUserId($request);
        $gameId = $request->input('game_id');
        
        if($this->gameRepository->isGameJoinable($gameId) === false){
            return response()->json(["error" => 'This game does not exist or is already finish'], 400);
        }

        // save user to game database
        $this->gameRepository->addUserToGame($userId, $gameId);

        // send back the game id the user joined
        return response()->json(['gameId' => $gameId], 200);
    }

    /**
     * Get games by status
     * 1 : Waiting for player
     * 2: Running
     * 3: Finish
     */
    public function getGamesByStatus(Request $request) : Response
    {
        // Validator rules
        $rules = ['id' => 'required|numeric|min:1|max:3'];
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json(["error" => $validator->errors()->all()], 400);
        }

        // get game by status
        $statusId = $request->input('id');
        $games = $this->gameRepository->getGameByStatus($statusId);

        return response()->json($games, 200);
    }

    /**
     * Get current game of the auth user
     */
    public function getUserRunningGame(Request $request) : Response
    {
        // get user id
        $userId = $this->jwt->getAuthUserId($request);

        // get user running game
        $runningGame = $this->gameRepository->getUserRunningGame($userId);
        
        // send back the game
        return response()->json($runningGame, 200);
    }
}
