<?php

namespace App\Repositories;

use App\Anime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AnimeRepository 
{


    public function getAll(int $level, int $musicType) : array
    {
        $sql = 
        "SELECT animes.id as idAnime, openings.id as idOpening, name_jap, 
        name_us, year, season, image, level, animes.type as animeType,
        myanimelist_id, anilist_id, kitsu_id, openings.type as openingType, number,
        title, artist, moe_link
        FROM animes
        INNER JOIN openings ON animes.id = openings.anime_id
        WHERE openings.type = ?
        AND animes.level <= ?";

        $result = DB::select($sql, [$musicType, $level]);
        
        return $result;
    }

    public function getAllByLevel(int $level) : array
    {
        $sql = 
        "SELECT animes.id as idAnime, openings.id as idOpening, name_jap, 
        name_us, year, season, image, level, animes.type as animeType,
        myanimelist_id, anilist_id, kitsu_id, openings.type as openingType, number,
        title, artist, moe_link
        FROM animes
        INNER JOIN openings ON animes.id = openings.anime_id
        WHERE animes.level <= ?";

        $result = DB::select($sql, [$level]);
        
        return $result;
    }




}