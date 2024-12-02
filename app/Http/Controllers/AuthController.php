<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }
    public function register(RegisterRequest $request){
        $result = $this->authService->register($request->validated());
        unset($result['user']['tokens']);
        return $this->success($result);
    }
    public function login(LoginRequest $request){
        $result = $this->authService->login($request->validated());
        unset($result['user']['tokens']);
        return $this->success($result);
    }
}
