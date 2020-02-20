<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameUser extends Model
{
    public $table = "game_user"; // define explicitely to allow creation with Eloquant
}
