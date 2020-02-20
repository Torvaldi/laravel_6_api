<?php

namespace App\Services;

use \Firebase\JWT\JWT;
use Illuminate\Http\Request;
use App\User;

class JwtService {

    public function genereToken($id, $username, $password) : string
    {
        $data = array(
            "id" => $id,
            "username" => $username,
            "password" => $password
        );
        return JWT::encode($data, env('JWTKEY'));
    }

    public function getAuthUserId(Request $request) : int
    {
        $token = $request->header('token');

        try {
            $tokenDecoded = JWT::decode($token, env('JWTKEY'), array('HS256'));
        } catch (\Throwable $th) {
            return array('error' => 'Invalid token'); 
        }

        return $tokenDecoded->id;
    }
}