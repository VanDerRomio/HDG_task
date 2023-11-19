<?php

namespace App\Http\Resources\User;

use App\Enums\UserRole;
use App\Http\Resources\Task\TaskCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name'              => $this->name,
            'role'              => UserRole::tryFrom($this->role)?->name ?: $this->role,
            'email'             => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'tasks'             => TaskCollection::collection($this->tasks),
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}