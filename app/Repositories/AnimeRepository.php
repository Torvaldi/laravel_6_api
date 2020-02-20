<?php

namespace App\Repositories;

use App\Anime;

class AnimeRepository 
{


    public function getAllByLevel(int $level)
    {
        return Anime::with('opening')->where('level', '=', $level)->get();
    }




}