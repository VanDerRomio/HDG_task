<?php

namespace App\Http\Controllers\Api;

use App\DTO\Task\PostTaskForm;
use App\DTO\Task\PutTaskForm;
use App\Helpers\ResponseStatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\Task\TaskCollection;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    public function __construct(Request $request){
        $this->setAuthenticatedUser($request);
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $tasks = Task::query()
            ->with('user')
            ->paginate(10);

        return $this->successResponse((new TaskCollection($tasks)));
    }

    /**
     * @param StoreTaskRequest $request
     * @param TaskService $taskService
     * @return JsonResponse
     */
    public function store(StoreTaskRequest $request, TaskService $taskService): JsonResponse
    {
        $postTaskForm = PostTaskForm::fromArray($request->validated());

        $task = $taskService->create($postTaskForm);

        if($task){
            return $this->successResponse(new TaskResource($task));
        }

        return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1016);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $task = Cache::remember(Task::class . ":{$id}", 60 * 10, function() use($id) {
            return Task::query()
                ->with('user')
                ->find($id);
        });

        if(!$task){
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1012);
        }

        return $this->successResponse((new TaskResource($task)));
    }

    /**
     * @param UpdateTaskRequest $request
     * @param string $id
     * @param TaskService $taskService
     * @return JsonResponse
     */
    public function update(
        UpdateTaskRequest $request,
        string $id,
        TaskService $taskService
    ): JsonResponse
    {
        $putTaskForm = PutTaskForm::fromArray($request->validated());

        $task = Task::query()
            ->findOrFail($id);

        $updatedTask = $taskService->update($task, $putTaskForm);

        if($updatedTask){
            return $this->successResponse(new TaskResource($updatedTask));
        }

        return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1017);
    }

    /**
     * @param string $id
     * @param TaskService $taskService
     * @return JsonResponse
     */
    public function changeState(string $id, TaskService $taskService): JsonResponse
    {
        $task = Task::query()
            ->findOrFail($id);

        $changeState = $taskService->changeState($task);

        if($changeState){
            return $this->successResponse();
        }

        return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1019);
    }

    /**
     * @param string $id
     * @param TaskService $taskService
     * @return JsonResponse
     */
    public function destroy(string $id, TaskService $taskService): JsonResponse
    {
        $task = Task::query()
            ->findOrFail($id);

        if($taskService->delete($task)){
            return $this->successResponse();
        }

        return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1018);
    }
}
