<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Game extends Model
{

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_creator_id');
    }

    public function user()
    {
        return $this->belongsToMany(User::class);
    }

    public function getGame() : array
    {
        return [
            'id' => $this->id,
            'userCreator' => [
                'id' => $this->creator->id,
                'username' => $this->creator->username
            ],
            'status' => $this->status,
            'level' => $this->level,
            'answer' => $this->answer,
            'scoreToWin' => $this->score_to_win,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

}
