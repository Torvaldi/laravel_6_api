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

    public function getUserById(int $id) : ?User
    {
        return User::where('id', '=', $id)->first();
    }

    public function getUserPassword(int $id) : string
    {
        $user = User::select('password')->where('id', '=', $id)->first();
        return $user->password;
    }


}