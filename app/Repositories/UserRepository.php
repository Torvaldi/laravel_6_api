<?php

namespace App\Repositories;

use App\User;

class UserRepository {

    public function getUserByUsername(string $username) : ?User
    {
        return User::where('username', '=', $username)->first();
    }

    public function getUserByEmail(string $email) : ?User
    {
        return User::where('email', '=', $email)->first();
    }

    public function getIdByUser(int $id) : ?User
    {
        return User::where('username', '=', $username)->first();
    }


}