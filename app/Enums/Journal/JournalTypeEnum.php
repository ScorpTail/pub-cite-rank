<?php

namespace App\Enums\Journal;

enum JournalTypeEnum: string
{
    case JOURNAL = 'A';
    case MAGAZINE = 'B';
    case NEWSPAPER = 'C';
    case BOOK = 'D';
    case THESIS = 'E';
    case REPORT = 'F';
    case CONFERENCE = 'G';
}
