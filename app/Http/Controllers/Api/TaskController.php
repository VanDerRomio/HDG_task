<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseStatusCodes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\Task\TaskCollection;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    /**
     * @return JsonResponse|JsonResource
     */
    public function index(): JsonResponse|JsonResource
    {
        $tasks = Task::query()
            ->with('user')
            ->paginate(10);

        return $this->successResponse((new TaskCollection($tasks)));
    }

    /**
     * @param StoreTaskRequest $request
     * @return JsonResponse|JsonResource
     */
    public function store(StoreTaskRequest $request): JsonResponse|JsonResource
    {
        $data = $request->validated();
    }

    /**
     * @param string $id
     * @return JsonResponse|JsonResource
     */
    public function show(string $id): JsonResponse|JsonResource
    {
        $task = Cache::remember(Task::class . ":{$id}", 60 * 10, function() use($id) {
            return Task::query()
                ->with('user')
                ->first($id);
        });

        if(!$task){
            return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1012);
        }

        return new TaskResource($task);
    }

    /**
     * @param UpdateTaskRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateTaskRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $task = Task::query()
            ->firstOrFail($id);

        if($task->delete()){
            return $this->successResponse();
        }

        return $this->errorResponse(ResponseStatusCodes::RESPONSE_STATUS_CODE_1014);
    }
}
