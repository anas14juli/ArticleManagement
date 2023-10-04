<?php

namespace App\Services;

use App\Http\Controllers\BaseController;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Repository\Eloquent\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService extends BaseController
{
    private $UserRepository;
    public function __construct(UserRepository $UserRepository)
    {
        $this->UserRepository = $UserRepository;
    }

    public function signUp(UserRegisterRequest $request)
    {
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $user = $this->UserRepository->create($input);

        $success['token'] =  $user->createToken('MyAuthApp')->plainTextToken;
        $success['name'] =  $user->name;
        $success['role'] =  $user->role;
        $success['email'] = $user->email;

        return $this->sendResponse($success, 'successfully registered.');
    }

    public function signIn(UserLoginRequest $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyAuthApp')->plainTextToken;
            $success['name'] = $user->name;
            $success['email'] = $user->email;
            $success['role'] = $user->role;
            return $this->sendResponse($success, 'Login successfully.');
        } else {
            return $this->sendError('please cheackyour Auth', ['error' => 'Unauthenticated']);
        }
    }
}
