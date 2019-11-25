<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JWTService;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\GameRepository;

class LobbyController extends Controller {

    public function __construct(JWTService $jwt, GameRepository $gameRepository)
    {
        $this->jwt = $jwt;
        $this->gameRepository = $gameRepository;
    }
    
    /**
     * Auth user create a game
     */
    public function createGame(Request $request) : Response
    {
        // get auth user
        $user = $this->jwt->getAuthUser($request);

        // check if user is not already in a game
        
        // validate request

        // save date to database
        
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
