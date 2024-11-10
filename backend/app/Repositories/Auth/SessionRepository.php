<?php

namespace App\Repositories\Auth;

use App\Helpers\ClientHelper;
use App\Helpers\RkeyHelper;
use Illuminate\Support\Facades\Redis;

class SessionRepository implements SessionRepositoryInterface
{
    protected int $oneDay = 86400;
    protected int $sysTimeNow = 0;

    public function __construct()
    {
        $this->sysTimeNow = time();
    }

    public function write($uid, $token, array $sessionData): bool
    {
        $ip = $sessionData['last_login_ip'];
        $newExpiredTime = $this->sysTimeNow + $this->oneDay;

        $clientTypeId = ClientHelper::getClientType();

        $clientId = ClientHelper::getClientId($uid, $ip);

        if ($oldSessionData = $this->checkClientSession($clientId)) {
            $lastLoginTime = $oldSessionData['last_login_time'];
        } else {
            $lastLoginTime = $this->sysTimeNow;
        }

        $newSessionData = array(
            "uid" => $uid,
            "client_type_id" => $clientTypeId,
            "expired_time" => $newExpiredTime,
            "last_login_ip" => $ip,
            "last_login_time" => $lastLoginTime,
            "token" => $token,
        );

        $saveData[$clientId] = $newSessionData;
        $cacheKey = RkeyHelper::getFormatKey(RkeyHelper::R_KEY_STRING_USER_SESSION, $clientId);
        if (Redis::set($cacheKey, json_encode($saveData), 'EX', $this->oneDay) === false) {
            return false;
        }
        return true;
    }

    public function checkClientSession($clientId)
    {
        if (empty($clientId)) {
            return false;
        }
        $cacheKey = RkeyHelper::getFormatKey(RkeyHelper::R_KEY_STRING_USER_SESSION, $clientId);
        $sessionJson = Redis::get($cacheKey);
        if (empty($sessionJson)) {
            return false;
        }
        $sessionData = json_decode($sessionJson,true);
        if (isset($sessionData[$clientId])) {
            return $sessionData[$clientId];
        }
        return false;
    }

    public function getSessionInfoByClientId($clientId)
    {
        $cacheKey = RkeyHelper::getFormatKey(RkeyHelper::R_KEY_STRING_USER_SESSION, $clientId);
        $sessionJson = Redis::get($cacheKey);
        if (empty($sessionJson)) {
            return false;
        }
        $sessionData = json_decode($sessionJson,true);
        if (empty($sessionData)) {
            return false;
        }
        return $sessionData;
    }

    public function destroyByClientId($clientId): true
    {
        $cacheKey = RkeyHelper::getFormatKey(RkeyHelper::R_KEY_STRING_USER_SESSION, $clientId);
        Redis::del($cacheKey);
        return true;
    }
}
