<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JwtService;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Repositories\UserRepository;

class AuthController extends Controller
{
    public $jwt;

    public function __construct(JwtService $jwt, UserRepository $userRepository)
    {
        $this->jwt = $jwt;
        $this->userRepository = $userRepository;
    }

    public function register(Request $request)
    {
        // check password confirmation
        if($request->input('password') !== $request->input('password_confirmation')){
            return response()->json(['error' => 'Password and password confirmation does not match']);
        }
        
        // validate request
        $request->validate([
            'username' => 'required|string|min:1|max:10',
            'password' => 'required|string|min:4|max:10',
            'email' => 'required|string|min:4'
        ]);

        $username = $request->input('username');
        // encode password
        $password = Hash::make($request->input('password'));
        $email = $request->input('email');

        // check if username exit
        $usernameExist = $this->userRepository->getUserByUsername($username);
        // user exist
        if ($usernameExist !== null) {
            return response()->json(['error' => 'This username is already taken']);
        }

        // check if mail exist
        $mailExist = $this->userRepository->getUserByEmail($email);
        // mail exist
        if ($mailExist !== null) {
            return response()->json(['error' => 'This email is already taken']);
        }

        // save user to database
        $user = new User;
        $user->username = $username;
        $user->password = $password;
        $user->email= $email;
        $user->save();

        return response()->json(['user' => $user]);
    }

    public function login(Request $request)
    {
        // validate request
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // get user from database
        $user = $this->userRepository->getUserByUsername($request->input('username'));
    
        // check user exist
        if($user === null){
            return response()->json(['error' => 'This username does not exist']);
        }

        // check password
        if (Hash::check($request->input('password'), $user->password) === false) {
            return response()->json(['error' => 'Password incorrect']);
        }

        // genere token
        $token = $this->jwt->genereToken($user->id, $user->username, $user->password);

        // return token
        return response()->json(['token' => $token]);
    }

    private function editPassword(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = $this->userRepository->getUserByUsername($request->input('username'));
        echo $user;
    }

}
