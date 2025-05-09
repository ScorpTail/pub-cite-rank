<?php

namespace App\Enums;

use App\Enums\BaseEnumTrait;

enum YesNoEnum: string
{
    use BaseEnumTrait;

    case YES = 'Y';
    case NO = 'N';
}
