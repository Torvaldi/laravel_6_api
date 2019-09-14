<?php

namespace App\Services;

use \Firebase\JWT\JWT;

class JwtService {

    public function genereToken($username, $password) : string
    {
        $data = array(
            "username" => $username,
            "password" => $password
        );
        return JWT::encode($data, env('JWTKEY'));
    }

}