<?php

namespace App\Repositories;

use App\Anime;
use Illuminate\Database\Eloquent\Collection;

class AnimeRepository 
{


    public function getAll(int $level, int $musicType) : Collection
    {
        return Anime::with(array('opening' => function($query) use (&$musicType){
            $query->where('type', $musicType);
        }))
        ->where('level', '=', $level)
        ->get();
    }

    public function getAllByLevel(int $level) : Collection
    {
        return Anime::with('opening')->where('level', '=', $level)->get();
    }




}