<?php

namespace App\Enums;

enum AbsenceType: string
{
    case VACATION = 'vacation';
    case SICK = 'sick';
    case OCR = 'ocr';

    public function label(): string {
        return match($this) {
            self::VACATION => 'Dovolená',
            self::SICK => 'Neschopenka',
            self::OCR => 'OČR',
        };
    }

}
