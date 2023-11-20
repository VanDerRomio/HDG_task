<?php

namespace App\Contracts\DTO;

interface IDTO{
    public static function fromArray(array $data, ?int $id = null);
}
