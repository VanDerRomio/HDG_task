<?php

namespace App\Services\States\Task;

use App\Contracts\Task\AState;
use Illuminate\Support\Facades\Log;

class DoneTaskState extends AState
{
    public function handle()
    {
        Log::info("current status is " . $this->task->status);
    }
}
