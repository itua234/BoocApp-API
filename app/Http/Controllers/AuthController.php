<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Http\Requests\{LoginRequest, VerifyAccount, 
    ResetPassword, ChangePassword, CreateUser, PasswordReset as PassReset};

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(CreateUser $request)
    {
        return $this->authService->register($request);
    }

    public function login(LoginRequest $request)
    {
        return $this->authService->login($request);
    }

    public function requestTokenGoogle(Request $request)
    {
        return $this->authService->requestTokenGoogle($request);
    }

    public function logout()
    {
        return $this->authService->logout();
    }

    public function refresh()
    {
        return $this->authService->refresh();
    }

    public function sendcode($email)
    {
        return $this->authService->sendverificationcode($email);
    }

    public function verifyEmail(VerifyAccount $request)
    {
        return $this->authService->verifyEmail($request);
    }

    public function verifyUserThroughWeb(VerifyAccount $request)
    {
        return $this->authService->verifyUserThroughWeb($request);
    }

    public function verifyResetToken(Request $request)
    {
        return $this->authService->verifyResetToken($request);
    }

    public function resetPassword(ResetPassword $request)
    {
        return $this->authService->resetPassword($request);
    }

    public function passwordReset(PassReset $request)
    {
        return $this->authService->passwordReset($request);
    }

    public function changePassword(ChangePassword $request)
    {
        return $this->authService->changePassword($request);
    }

    public function createAdmin(CreateUser $request)
    {
        return $this->authService->createAdmin($request);
    }

}
