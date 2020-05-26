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
            'musicType' => 'required|numeric|min:0|max:2'
        ]);

        if($validator->fails()){
            return response()->json(["error" => $validator->errors()->all()], 400);
        }

        // get animes
        $level = (int) $request->input('level');
        $musicType = (int) $request->input('musicType');
        
        if($musicType !== 0 && $musicType !== 1){
            $animes = $this->animeRepository->getAllByLevel($level);
        } else {
            $animes = $this->animeRepository->getAll($level, $musicType);
        }
        // return animes
        return response()->json($animes, 200);
    }
}
