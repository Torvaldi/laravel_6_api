<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class GameController extends Controller
{

    /**
     * Get all the players of a given game
     */
    public function getGamePlayers(Request $request) : Response
    {
        //
    }


    /**
     * update the game status
     * 1 : Waiting for player
     * 2: Running
     * 3: Finish
     */
    public function updateGameStatus(Request $request) : Response
    {
        //
    }

    /**
     * Auth user leave an unfinised game
     */
    public function userLeaveGame(Request $request) : Response
    {
        //
    }
}
