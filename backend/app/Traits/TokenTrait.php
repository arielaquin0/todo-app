<?php

namespace App\Traits;

use App\Helpers\TokenHelper;

trait TokenTrait
{
    public function getNewToken($uid)
    {
        return TokenHelper::newToken($uid);
    }

    public function unPackToken($token): false|array
    {
        return TokenHelper::unPackToken($token);
    }

}
