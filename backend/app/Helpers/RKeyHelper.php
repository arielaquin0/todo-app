<?php
namespace App\Helpers;

class RKeyHelper
{
    const R_KEY_STRING_USER_SESSION = "users:session:{i}";

    static function getFormatKey($key, ...$args): array|string
    {
        $argList = func_get_args();
        array_shift($argList);
        $replaceArr = array();
        for ($i=0; $i<count($argList); $i++) {
            $replaceArr[$i] = "{i}";
        }
        return str_replace($replaceArr, $argList, $key);
    }

}
