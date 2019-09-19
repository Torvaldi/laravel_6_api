<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JWTService;

class LobbyController extends Controller {

    public function __construct(JWTService $jwt){
        $this->jwt = $jwt;
    }
    
    //(/game.store)
    public function createGame(Request $request){
        // get auth user
        $user = $this->jwt->getAuthUser($request);

        // check if user is not already in a game
        
        
        // validate request

        // save date to database
        
    }

    //(/game.store.join)
    public function gameJoin(Request $request){

    }

    //(/game.index.status)
    public function indexGameStatus(Request $request){

    }

    //(/game.index.running)
    public function indexGameRunning(Request $request){

    }
}
