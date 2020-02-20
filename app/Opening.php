<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Anime;

class Opening extends Model
{
    public function Anime()
    {
        $this->belongsTo(Anime::class);
    }
}
