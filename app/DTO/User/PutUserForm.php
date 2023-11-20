<?php

namespace App\DTO\User;

use App\Enums\UserRole;

readonly class PutUserForm
{
    public int $id;
    public ?string $name;
    public ?string $email;
    public ?string $password;
    public ?string $role;

    public function __construct(
        int $id,
        ?string $name       = null,
        ?string $email      = null,
        ?string $password   = null,
        ?string $role       = null
    ){
        $this->id       = $id;
        $this->name     = $name;
        $this->email    = $email;
        $this->password = $password;
        $this->role     = $role ?: UserRole::User->value;
    }

    public static function fromArray(array $data, int $id): self{
        return new self(
            id:         $id,
            name:       $data['name']       ?? null,
            email:      $data['email']      ?? null,
            password:   $data['password']   ?? null,
            role:       $data['role']       ?? null,
        );
    }
}
