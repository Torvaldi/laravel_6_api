<?php

namespace App\Repositories;

use App\Anime;

class AnimeRepository 
{


    public function getAll(int $level, int $musicType)
    {
        return Anime::with(array('opening' => function($query) use (&$musicType){
            $query->where('type', $musicType);
        }))
        ->where('level', '=', $level)
        ->get();
    }

    public function getAllByLevel(int $level)
    {
        return Anime::with('opening')->where('level', '=', $level)->get();
    }




}