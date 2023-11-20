<?php

namespace App\Services;

use App\Contracts\Task\AState;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Services\States\Task\DoneTaskState;
use App\Services\States\Task\InProcessingTaskState;
use App\Services\States\Task\NewTaskState;

class TaskStateManager
{
    private AState $state;
    public Task $task;

    public function __construct(Task $task){
        $this->task = $task;

        $this->initializeState();
    }

    private function initializeState(): void{
        $taskStatusAsEnum = TaskStatus::tryFrom($this->task->status);

        $this->state = match ($taskStatusAsEnum){
            TaskStatus::New             => new NewTaskState($this),
            TaskStatus::InProcessing    => new InProcessingTaskState($this),
            TaskStatus::Done            => new DoneTaskState($this),

            default => throw new \Exception("Unknown state: {$this->task->status}")
        };
    }

    public function getCurrentState(): AState{
        return $this->state;
    }

    public function changeState(AState $state): void{
        $this->state = $state;
    }

    public function tryChangeState(): bool{
        try {
            $this->state->handle();

            return true;
        } catch (\Exception $exception){}

        return false;
    }
}
