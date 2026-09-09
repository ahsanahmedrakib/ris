<?php

namespace App\Enums;

enum ExamType: string
{
    case Quiz = 'quiz';
    case Midterm = 'midterm';
    case Final = 'final';
    case Assignment = 'assignment';

    public function label(): string
    {
        return match ($this) {
            self::Quiz => 'কুইজ',
            self::Midterm => 'অর্ধবার্ষিক',
            self::Final => 'বার্ষিক',
            self::Assignment => 'এসাইনমেন্ট',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Quiz => 'bg-cyan-100 text-cyan-800',
            self::Midterm => 'bg-amber-100 text-amber-800',
            self::Final => 'bg-red-100 text-red-800',
            self::Assignment => 'bg-violet-100 text-violet-800',
        };
    }
}
