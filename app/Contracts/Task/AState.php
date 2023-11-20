<?php

namespace App\Contracts\Task;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Services\TaskStateManager;

abstract class AState{
    protected Task $task;

    public function __construct(protected TaskStateManager $taskStateManager){
        $this->task = $this->taskStateManager->task;
    }

    protected function updateTaskStatus(TaskStatus $status): void{
        $this->task->status = $status->value;

        if(!$this->task->save()){
            throw new \Exception("Fail save task status");
        }
    }

    abstract public function handle();
}
