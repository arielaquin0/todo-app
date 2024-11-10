<?php

namespace App\Helpers;

class Base64UrlHelper
{
    public static function base64urlDecode($data): false|string
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) + (strlen($data) % 4), '=', STR_PAD_RIGHT));
    }

    public static function base64urlEncode($data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
