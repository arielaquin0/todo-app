<?php

namespace App\Repositories\Auth;

use App\Helpers\ClientHelper;
use App\Helpers\PasswordHelper;
use App\Helpers\TokenHelper;
use App\Models\User;
use App\Traits\ResponseTrait;
use App\Traits\TokenTrait;

class AuthRepository implements AuthRepositoryInterface
{
    use TokenTrait;
    use ResponseTrait;

    protected int $sysTimeNow = 0;

    public function __construct(
        private readonly User $userModel,
        private readonly SessionRepository $sessionRepository
    ) {
        $this->sysTimeNow = time();
    }

    public function authByToken($token, $clientIp): array
    {
        $result = $this->unPackToken($token);

        if (empty($result)) {
            return $this->json(401, 'Authentication failed.');
        }

        $uid = $result['uid'];
        $userInfo = $this->userModel->findRowById($uid);

        if (empty($userInfo)) {
            return $this->json(404, 'User does not exist.');
        }

        $clientId = ClientHelper::getClientId($uid, $clientIp);
        $userInfo['token'] = $token;
        $sessionInfo = $this->sessionRepository->getSessionInfoByClientId($clientId);

        if (empty($sessionInfo)) {
            return $this->json(401, 'Login credential has expired, Please re-login.');
        }

        $sessionData = array(
            "last_login_ip" => $clientIp
        );
        if (isset($sessionInfo[$clientId])) {
            $mySessionData = $sessionInfo[$clientId];
            if ($mySessionData['token'] != $token) {
                return $this->json(401, 'Invalid token.');
            }

            $expiredTime = $mySessionData['expired_time'];

            if (($this->sysTimeNow - $expiredTime) > 0) {
                return $this->json(401, 'Token has expired, Please re-login.');
            }
        }

        if ($this->sessionRepository->write($uid, $token, $sessionData) === false) {
            return $this->json(500, 'Unable to save session.');
        }

        return $this->json(200, 'ok', $userInfo);
    }

    public function doLogin(array $loginData): array
    {
        $username = $loginData['username'];
        $password = $loginData['password'];
        $clientIp = $loginData['clientIp'];

        $userInfo = $this->userModel->findRow(array('username' => $username));

        if (empty($userInfo)) {
            return $this->json(404, 'User does not exist');
        }

        if (PasswordHelper::checkHashPassword($password, $userInfo['password_salt'], $userInfo['password']) === false) {
            return $this->json(401, 'Password is incorrect');
        }

        $uid = intval($userInfo['id']);
        $sessionData = array(
            'last_login_ip' => $clientIp,
        );
        $token = TokenHelper::newToken($uid);

        if ($this->sessionRepository->write($uid, $token, $sessionData) !== true) {
            return $this->json(500, 'Unable to save session.');
        }

        $userInfo['token'] = $token;

        return $this->json(200, 'ok', $userInfo);
    }

    public function logout($clientId): true
    {
        return $this->sessionRepository->destroyByClientId($clientId);
    }
}
