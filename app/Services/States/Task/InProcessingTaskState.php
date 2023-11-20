<?php

namespace App\Services\States\Task;

use App\Contracts\Task\AState;
use App\Enums\TaskStatus;
use Illuminate\Support\Facades\Log;

class InProcessingTaskState extends AState
{
    public function handle()
    {
        $this->updateTaskStatus(TaskStatus::Done);

        $this->taskStateManager->changeState(new DoneTaskState($this->taskStateManager));

        Log::info("current status is " . $this->task->status);
    }
}
