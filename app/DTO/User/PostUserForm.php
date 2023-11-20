<?php

namespace App\DTO\User;

use App\Enums\UserRole;

readonly class PostUserForm
{
    public string $name;
    public string $email;
    public string $password;
    public string $role;

    public function __construct(
        string $name,
        string $email,
        string $password,
        ?string $role = null
    ){
        $this->name     = $name;
        $this->email    = $email;
        $this->password = $password;
        $this->role     = $role ?: UserRole::User->value;
    }

    public static function fromArray(array $data): self{
        return new self(
            name:       $data['name'],
            email:      $data['email'],
            password:   $data['password'],
            role:       $data['role'],
        );
    }
}
