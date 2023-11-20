<?php

namespace App\Services;

use App\DTO\Task\PostTaskForm;
use App\DTO\Task\PutTaskForm;
use App\Models\Task;

class TaskService
{
    /**
     * @param PostTaskForm $form
     * @return Task|false
     */
    public function create(PostTaskForm $form): Task|false{
        $task = new Task();

        $task->user_id      = $form->userId;
        $task->title        = $form->title;
        $task->description  = $form->description;
        $task->status       = $form->status;

        if($task->save()){
            return $task;
        }

        return false;
    }

    /**
     * @param Task $task
     * @param PutTaskForm $form
     * @return Task|false
     */
    public function update(Task $task, PutTaskForm $form): Task|false{
        $task->user_id      = $form->userId;
        $task->title        = $form->title;
        $task->description  = $form->description;
        $task->status       = $form->status;

        if ($task->save()) {
            return $task;
        }

        return false;
    }

    /**
     * @param Task $task
     * @return bool
     */
    public function delete(Task $task): bool{
        if($task->delete()){
            return true;
        }

        return false;
    }
}
