<?php
namespace App\Helpers;

class PasswordHelper
{
    public static function getPasswordSalt(): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i=0; $i < 4; $i++) {
            $password .= $chars[ mt_rand(0, strlen($chars) -1 )];
        }
        return $password;
    }

    public static function getHashPassword($pwd, $salt): string
    {
        return password_hash($pwd.$salt, PASSWORD_DEFAULT);
    }

    public static function checkHashPassword($pwd, $salt, $hash): bool
    {
        return password_verify($pwd.$salt, $hash);
    }
}
