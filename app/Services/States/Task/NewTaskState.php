<?php

namespace App\Services\States\Task;

use App\Contracts\Task\AState;
use App\Enums\TaskStatus;
use Illuminate\Support\Facades\Log;

class NewTaskState extends AState
{
    public function handle()
    {
        $this->updateTaskStatus(TaskStatus::InProcessing);

        $this->taskStateManager->changeState(new InProcessingTaskState($this->taskStateManager));

        Log::info("current status is " . $this->task->status);
    }
}
