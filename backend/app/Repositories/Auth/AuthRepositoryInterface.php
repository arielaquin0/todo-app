<?php

namespace App\Repositories\Auth;

interface AuthRepositoryInterface
{
    public function authByToken($token, $clientIp);
    public function doLogin(array $loginData);
    public function logout($clientId);
}
