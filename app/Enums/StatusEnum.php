<?php

namespace App\Enums;

enum StatusEnum: string
{
    use BaseEnumTrait;

    case ACTIVE = 'A';
    case HIDDEN = 'H';
    case DISABLED = 'D';
}
