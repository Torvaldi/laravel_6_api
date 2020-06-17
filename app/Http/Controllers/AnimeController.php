<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\JwtService;
use App\Repositories\AnimeRepository;
use Validator;

class AnimeController extends Controller
{
    public $jwt;
    public $animeRepository;

    public function __construct(JwtService $jwt, AnimeRepository $animeRepository)
    {
        $this->jwt = $jwt;
        $this->animeRepository = $animeRepository;
    }

    /**
     * Get anime with the given parameters
     */
    public function index(Request $request) : Response
    {
        // validator
        $validator = Validator::make($request->all(), [
            'level' => 'required|numeric|min:1|max:3',
            'musicType' => 'required|numeric|min:1|max:3'
        ]);

        if($validator->fails()){
            return response()->json(["error" => $validator->errors()->all()], 400);
        }

        // get animes
        $level = (int) $request->input('level');
        $musicType = (int) $request->input('musicType');
        
        /**
         * Note music type
         * 1: all
         * 2: opening
         * 3: ending 
         */

        // if music type is 1, get all animes's musicby level, regardless of the op and end
        if($musicType === 1){
            $animes = $this->animeRepository->getAllByLevel($level);
        } else if($musicType === 2){ // opening
            $animes = $this->animeRepository->getAll($level, 0); // 0 is the tiny int value for opening in the databse
        } else { // ending 
            $animes = $this->animeRepository->getAll($level, 1);// 1 is the tiny int value for ending in the databse
        }
        
        return response()->json($animes, 200);
    }
}
