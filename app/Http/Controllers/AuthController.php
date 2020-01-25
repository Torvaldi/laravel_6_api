<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JwtService;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Repositories\UserRepository;
use App\Http\Resources\User as UserResource;
use Validator;

class AuthController extends Controller
{
    public $jwt;
    private $userData;
    private $passwordHash;

    public function __construct(JwtService $jwt, UserRepository $userRepository)
    {
        $this->jwt = $jwt;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request, username, password, password_confirmation, email
     * @return json user data in json, (id, name and email)
     * Route : /api/register
     */
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

        return response()->json($user);
    }

    /**
     * @param Request $request (username, password)
     * Route api/login
     */
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

    /**
     * @param Request $request, oldPassword, newPassword, confirmNewPassword
     * Route api/password
     */
    public function editPassword(Request $request)
    {
        $rules = [
            'oldPassword' => 'required|string|min:4|max:10',
            'newPassword' => 'required|string|min:4|max:10',
            'confirmNewPassword' => 'required|string|min:4|max:10',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json(["error" => $validator->errors()->all()], 400);
        }
        
        // check if new password is the same as the confirmation password
        if ($request->newPassword != $request->confirmNewPassword){
            return response()->json(['error' => 'Password and password confirmation does not match']);
        }

        // retrive user with token
        $userId = $this->jwt->getAuthUserId($request);
        $currentPassword = $this->userRepository->getUserPassword($userId);

        // check if the old password given by the user is the same as the current password
        if (Hash::check($request->input('oldPassword'), $currentPassword) === false){
            return response()->json(['error' => 'You password is incorrect']);
        }

        // hash the new password
        $passwordHash = Hash::make($request->input('newPassword'));

        // upadate new password
        $user = User::find($userId);
        $user->password = $passwordHash;
        $user->save();

        return response()->json($user);
    }

}