<?php

namespace App\Services;

class Formatter
{
    /**
     * Create 3 demantional array containing animes with each animes containing their opening/ending
     */
    public function formatAnime(array $animes) : array
    {
        $result = [];
        foreach($animes as $anime){
            $animeArray = (array) $anime;
            $item = [];
            
            $idAnime = $animeArray['idAnime'];

            // define animes data
            if(array_key_exists($idAnime, $result) === false){
                $item = [
                    'id' => $idAnime,
                    'name_jap' => $animeArray['name_jap'],
                    'name_us' => $animeArray['name_us'],
                    'name_jap' => $animeArray['name_jap'],
                    'year' => $animeArray['year'],
                    'season' => $animeArray['season'],
                    'image' => $animeArray['image'],
                    'level' => $animeArray['level'],
                    'type' => $animeArray['animeType'],
                    'myanimelist_id' => $animeArray['myanimelist_id'],
                    'anilist_id' => $animeArray['anilist_id'],
                    'kitsu_id' => $animeArray['kitsu_id'],
                ];
            } else {
                $item = $result[$idAnime];
            }

            // define the opening data
            $item['opening'][] = [
                'id' => $animeArray['idOpening'],
                'type' => $animeArray['openingType'],
                'number' => $animeArray['number'],
                'title' => $animeArray['title'],
                'artist' => $animeArray['artist'],
                'moe_link' => $animeArray['moe_link'],
                'anime_id' => $idAnime,
            ];

            // define result
            $result[$idAnime] = $item;
        }

        // reset all key values
        $result = array_values($result);

        return $result;
    }
}