<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Opening;

class Anime extends Model
{
    public function opening()
    {
        return $this->hasMany(Opening::class);
    }
}
