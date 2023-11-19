<?php

namespace App\Http\Resources\Task;

use App\Enums\TaskStatus;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

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
            'id'                => $this->id,
            'user'              => new UserResource($this->whenLoaded('user')),
            'title'             => $this->title,
            'description'       => $this->description,
            'description_small' => Str::limit($this->description, 30),
            'status'            => TaskStatus::tryFrom($this->status)?->name ?: $this->status,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
