<?php

namespace App\Enums;

enum UserRole:string {
    case User   = 'user';
    case Admin  = 'admin';

    public static function namesAsArray(): array{
        $roles = [];

        foreach (self::cases() as $role) {
            $roles[] = $role->name;
        }

        return $roles;
    }

    public static function valuesAsArray(): array{
        $values = [];

        foreach (self::cases() as $value) {
            $values[] = $value->value;
        }

        return $values;
    }
}
