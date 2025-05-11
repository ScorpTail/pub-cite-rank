<?php

namespace App\Enums\Journal;

use App\Enums\BaseEnumTrait;

enum JournalTypeEnum: string
{
    use BaseEnumTrait;

    case JOURNAL = 'A';
    case MAGAZINE = 'B';
    case NEWSPAPER = 'C';
    case BOOK = 'D';
    case THESIS = 'E';
    case REPORT = 'F';
    case CONFERENCE = 'G';
}
