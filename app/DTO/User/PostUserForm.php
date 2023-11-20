<?php

namespace App\DTO\User;

use App\Contracts\DTO\IDTO;
use App\Enums\UserRole;

readonly class PostUserForm implements IDTO
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

    public static function fromArray(array $data, ?int $id = null): self{
        return new self(
            name:       $data['name'],
            email:      $data['email'],
            password:   $data['password'],
            role:       $data['role'],
        );
    }
}
