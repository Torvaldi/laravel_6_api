<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JwtService;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\GameRepository;
use App\User;
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
            'score_to_win' => 'required|numeric|min:10|max:500'
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
        //
    }

    /**
     * Get games by status
     * 1 : Waiting for player
     * 2: Running
     * 3: Finish
     */
    public function getGamesByStatus(Request $request) : Response
    {
        //
    }

    /**
     * Get current game of the auth user
     */
    public function getUserRunningGame(Request $request) : Response
    {
        //
    }
}
