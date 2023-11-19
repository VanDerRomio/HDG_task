<?php

namespace App\Enums;

enum TaskStatus:string {
    case New            = 'new';
    case InProcessing   = 'in_processing';
    case Done           = 'done';
}
