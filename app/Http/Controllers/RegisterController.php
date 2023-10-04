<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class RegisterController extends BaseController
{
    private $MyServices;
    public function __construct(UserService $MyServices)
    {
        $this->MyServices = $MyServices;
    }

    public function signUp(UserRegisterRequest $request)
    {
        return $this->MyServices->signUp($request);
    }

    public function signIn(UserLoginRequest $request)
    {
        return $this->MyServices->signIn($request);
    }
}
