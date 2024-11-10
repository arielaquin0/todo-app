<?php

namespace App\Http\Controllers;

use App\Helpers\ClientHelper;
use App\Helpers\ClientIpHelper;
use App\Models\AuditTrail;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

class BaseController extends Controller
{
    protected int $clientIp = 0;
    protected string $clientId = "";
    public int $uid = 0;
    protected object $userInfo;
    protected string $path = '';
    protected array $noAuthList = array(
        'api/login',
    );
    protected $authRepository;
    protected $auditTrailModel;

    public function __construct()
    {
        $this->userInfo = (object)[];
        $this->authRepository = App::make("App\Repositories\Auth\AuthRepository");
        $path = Request::path();
        $this->clientIp = ip2long(ClientIpHelper::getClientIp());
        $this->auditTrailModel = new AuditTrail();

        if ($this->checkIsNeedAuth($path) === true) {
            $token = Request::header("token", "");

            if (empty($token)) {
                $response = new JsonResponse(['message' => 'Unauthorized access'], 401);
                $response->send();
                exit;
            }

            $this->checkAuth($token);
        }
    }

    protected function checkAuth(string $token): void
    {
        $response = $this->authRepository->authByToken($token, $this->clientIp);

        if ($response['code'] > 200) {
            $response = new JsonResponse(['message' => $response['msg']], $response['code']);
            $response->send();
            exit;
        }

        $this->userInfo = $response['data'];
        $this->uid = $this->userInfo->id;
        $this->clientId = ClientHelper::getClientId($this->uid, $this->clientIp);
    }

    protected function checkIsNeedAuth(string $path): bool
    {
        if (in_array($path, $this->noAuthList)) {
            return false;
        }

        return true;
    }

    protected function logAuditTrail(
        string $tableName,
        string $action,
        array $details,
        ?int $uid = null
    ): void {
        $this->auditTrailModel->create([
            'user_id' => $uid ?? $this->uid,
            'table_name' => $tableName,
            'action' => $action,
            'details' => json_encode($details),
        ]);
    }
}
