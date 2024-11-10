<?php

namespace App\Helpers;

class ClientHelper
{
    const CLIENT_TYPE_WINDOWS_PC_WEB = 1100;
    const CLIENT_TYPE_MAC_PC_WEB = 1101;
    const CLIENT_TYPE_UNKNOWN = 1404;

    public static function getClientType(): int
    {
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : false;
        if (stripos($agent, 'windows nt') !== false) {
            return self::CLIENT_TYPE_WINDOWS_PC_WEB;
        } elseif (stripos($agent, 'mac os') !== false) {
            return self::CLIENT_TYPE_MAC_PC_WEB;
        } else {
            return self::CLIENT_TYPE_UNKNOWN;
        }
    }

    public static function getClientId($uid, $clientIp): string
    {
        $clientTypeId = self::getClientType();
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : 'unknown';
        return sha1($userAgent.$uid.$clientTypeId.$clientIp);
    }
}
