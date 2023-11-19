<?php

namespace App\Http\Resources\Task;

use App\Enums\TaskStatus;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'user'          => UserResource::make($this->user),
            'title'         => $this->title,
            'description'   => $this->description,
            'status'        => TaskStatus::tryFrom($this->status)?->name ?: $this->status,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
