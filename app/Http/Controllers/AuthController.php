<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JwtService;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Repositories\UserRepository;
use Validator;
use Symfony\Component\HttpFoundation\Response;

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
    public function register(Request $request) : Response
    {
        // check password confirmation
        if($request->input('password') !== $request->input('password_confirmation')){
            return response()->json(['error' => ['Password and password confirmation does not match']], 400);
        }
        
        // validate request
        $rules = [
            'username' => 'required|string|min:1|max:10',
            'password' => 'required|string|min:4|max:10',
            'email' => 'required|string|min:4'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json(["error" => $validator->errors()->all()], 400);
        }

        $username = $request->input('username');
        // encode password
        $password = Hash::make($request->input('password'));
        $email = $request->input('email');

        // check if username exit
        $usernameExist = $this->userRepository->getUserByUsername($username);
        // user exist
        if ($usernameExist !== null) {
            return response()->json(['error' => ['This username is already taken']], 400);
        }

        // check if mail exist
        $mailExist = $this->userRepository->getUserByEmail($email);
        // mail exist
        if ($mailExist !== null) {
            return response()->json(['error' => ['This email is already taken']], 400);
        }

        // save user to database
        $user = new User;
        $user->username = $username;
        $user->password = $password;
        $user->email= $email;
        $user->save();

        return response()->json($user, 200);
    }

    /**
     * @param Request $request (username, password)
     * Route api/login
     */
    public function login(Request $request): Response
    {
        // validate request
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json(["error" => $validator->errors()->all()], 400);
        }

        // get user from database
        $user = $this->userRepository->getUserByUsername($request->input('username'));
    
        // check user exist
        if($user === null){
            return response()->json(['error' => ['This username does not exist']], 400);
        }

        // check password
        if (Hash::check($request->input('password'), $user->password) === false) {
            return response()->json(['error' => ['Password incorrect']], 400);
        }

        // genere token
        $token = $this->jwt->genereToken($user->id, $user->username, $user->password);

        // return token
        return response()->json(['token' => $token], 200);
    }

    /**
     * @param Request $request, oldPassword, newPassword, confirmNewPassword
     * Route api/password
     */
    public function editPassword(Request $request) : Response
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
            return response()->json(['error' => ['Password and password confirmation does not match']], 400);
        }

        // retrive user with token
        $userId = $this->jwt->getAuthUserId($request);
        $currentPassword = $this->userRepository->getUserPassword($userId);

        // check if the old password given by the user is the same as the current password
        if (Hash::check($request->input('oldPassword'), $currentPassword) === false){
            return response()->json(['error' => ['Your password is incorrect']], 400);
        }

        // hash the new password
        $passwordHash = Hash::make($request->input('newPassword'));

        // upadate new password
        $user = User::find($userId);
        $user->password = $passwordHash;
        $user->save();

        return response()->json($user, 200);
    }

}