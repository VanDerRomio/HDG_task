<?php

namespace App\DTO\Task;

use App\Contracts\DTO\IDTO;
use App\Enums\TaskStatus;

readonly class PutTaskForm implements IDTO
{
    public int $id;
    public int $userId;
    public string $title;
    public ?string $description;
    public string $status;

    public function __construct(
        int $id,
        int $userId,
        string $title,
        ?string $description = null,
        ?string $status = null
    ){
        $this->id           = $id;
        $this->userId       = $userId;
        $this->title        = $title;
        $this->description  = $description;
        $this->status       = $status ?: TaskStatus::New->value;
    }

    public static function fromArray(array $data, ?int $id = null): self{
        return new self(
            id:             $id,
            userId:         $data['user_id'],
            title:          $data['title'],
            description:    $data['description']    ?? null,
            status:         $data['status']         ?? null,
        );
    }
}
