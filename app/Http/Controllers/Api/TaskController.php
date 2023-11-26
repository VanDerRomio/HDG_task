<?php

namespace App\Http\Controllers\Api;

use App\DTO\Task\PostTaskForm;
use App\DTO\Task\PutTaskForm;
use App\Enums\UserRole;
use App\Helpers\ResponseStatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\Task\TaskCollection;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use App\Models\User;
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
        if($this->authenticatedUser->cannot('viewAny', Task::class)){
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1003);
        }

        $userId = $this->authenticatedUser->id;

        $tasks = Task::query()
            ->with('user')
            ->when($this->authenticatedUser->role === UserRole::User->value, function($query) use($userId){
                return $query->where('user_id', $userId);
            })
            ->paginate(10);

        return $this->successResponse((new TaskCollection($tasks)));
    }

    /**
     * @param StoreTaskRequest $request
     * @param TaskService $taskService
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreTaskRequest $request, TaskService $taskService): JsonResponse
    {
        if($this->authenticatedUser->cannot('create', Task::class)){
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1003);
        }

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

        if($this->authenticatedUser->cannot('view', $task)){
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1003);
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
        $putTaskForm = PutTaskForm::fromArray($request->validated(), $id);

        $task = Task::query()
            ->findOrFail($id);

        if($this->authenticatedUser->cannot('update', $task)){
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1003);
        }

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

        if($this->authenticatedUser->cannot('update', $task)){
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1003);
        }

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

        if($this->authenticatedUser->cannot('delete', $task)){
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1003);
        }

        if($taskService->delete($task)){
            return $this->successResponse();
        }

        return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1018);
    }
}
