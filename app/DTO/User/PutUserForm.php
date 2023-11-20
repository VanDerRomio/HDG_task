<?php

namespace App\DTO\User;

use App\Enums\UserRole;

readonly class PutUserForm
{
    public int $id;
    public string $name;
    public string $email;
    public string $password;
    public string $role;

    public function __construct(
        int $id,
        string $name,
        string $email,
        string $password,
        ?string $role = null
    ){
        $this->id       = $id;
        $this->name     = $name;
        $this->email    = $email;
        $this->password = $password;
        $this->role     = $role ?: UserRole::User->value;
    }

    public static function fromArray(array $data): self{
        return new self(
            id:         $data['id'],
            name:       $data['name'],
            email:      $data['email'],
            password:   $data['password'],
            role:       $data['role'] ?? null,
        );
    }
}
