<?php
declare(strict_types=1);

namespace App\Enums;

enum PriorityType: string {
    case STANDARD = 'standard';
    case HIGH = 'high';
}
