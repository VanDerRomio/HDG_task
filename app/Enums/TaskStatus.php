<?php

namespace App\Enums;

enum TaskStatus:string {
    case New            = 'new';
    case InProcessing   = 'in_processing';
    case Done           = 'done';

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
