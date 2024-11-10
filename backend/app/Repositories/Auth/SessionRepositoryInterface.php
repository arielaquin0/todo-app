<?php

namespace App\Repositories\Auth;

interface SessionRepositoryInterface
{
    public function write($uid, $token, array $sessionData);
    public function checkClientSession($clientId);
    public function getSessionInfoByClientId($clientId);
    public function destroyByClientId($clientId);
}
