<?php
namespace App\Helpers;

class UtilHelper
{
    public static function generateWord($length = 8): string
    {
        $word = '';
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $charLen = strlen($chars);
        for ($i=0; $i<$length; $i++) {
            $loop = mt_rand(0, ($charLen - 1));
            $word .= $chars[$loop];
        }

        return $word;
    }
}
