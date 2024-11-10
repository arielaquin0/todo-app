<?php
namespace App\Helpers;

class ClientIpHelper
{
    public static function getClientIp()
    {
        $http = getenv("HTTP_X_FORWARDED_FOR");

        if ($http) {
            $var = explode(",", $http);

            if (empty($var)) {
                return "0.0.0.0";
            }

            if (!isset($var[0])) {
                return "0.0.0.0";
            }

            $ip = $var[0];
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $ip = \Request::getClientIp();
            }
        } else {
            $ip = \Request::getClientIp();
        }

        return $ip;
    }
}
