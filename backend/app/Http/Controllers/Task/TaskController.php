<?php

namespace App\Http\Controllers\Task;

use App\Enums\AuditAction;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends BaseController
{
    public function __construct(
        private readonly Task $taskModel,
    ) {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('rowsPerPage', 10);
        $status = $request->input('status');
        $sortBy = $request->input('sortBy');
        $descending = $request->input('descending') === 'true';

        $where = $status ? ['status' => $status] : [];

        $sort = [
            'column' => $sortBy ?? 'updated_at',
            'direction' => $descending ? 'desc' : 'asc'
        ];

        $tasks = $this->taskModel->paginateRows($page, $perPage, $where, ['*'], $sort);

        return response()->json($tasks);
    }

    public function store(TaskStoreRequest $request): JsonResponse
    {
        $taskData = $request->validated();

        $result = $this->taskModel->create($taskData);

        $this->logAuditTrail(
            $this->taskModel->getTable(),
            AuditAction::INSERT->value,
            [ 'id' => $result->id ] // Only storing task ID for now
        );

        return response()->json($result);
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        $taskData = $request->all();

        $task->update($taskData);

        $this->logAuditTrail(
            $this->taskModel->getTable(),
            AuditAction::UPDATE->value,
            [ 'id' => $task->id ] // Only storing task ID for now
        );

        return response()->json($taskData);
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        $this->logAuditTrail(
            $this->taskModel->getTable(),
            AuditAction::DELETE->value,
            [ 'id' => $task->id ] // Only storing task ID for now
        );

        return response()->json(['message' => 'Task deleted']);
    }


}
