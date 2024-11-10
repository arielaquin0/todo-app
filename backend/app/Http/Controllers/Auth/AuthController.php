<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AuditAction;
use App\Models\User;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;

class AuthController extends BaseController
{
    public function __construct(
        private readonly User $userModel,
    ) {
        parent::__construct();
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $loginData = array(
            "username" => strtolower(trim($data["username"])),
            "password" => trim($data["password"]),
            "clientIp" => $this->clientIp,
        );

        $re = $this->authRepository->doLogin($loginData);

        if ($re['code'] == 200) {
            $userInfo = $re['data'];

            $result = array(
                'token' => $userInfo['token'],
                'uid'   => $userInfo['id'],
            );

            $this->logAuditTrail(
                $this->userModel->getTable(),
                AuditAction::LOGIN->value,
                ['ip' => long2ip($this->clientIp)],
                $result['uid']
            );

            return response()->json([
                'message'  => 'ok',
                'data' => $result,
            ]);
        }

        return response()->json(['message' => $re['message']], $re['code']);
    }

    public function logout(): JsonResponse
    {
        $this->authRepository->logout($this->clientId);

        $this->logAuditTrail(
            $this->userModel->getTable(),
            AuditAction::LOGOUT->value,
            ['ip' => long2ip($this->clientIp)]
        );

        return response()->json(['message'  => 'ok']);
    }
}
