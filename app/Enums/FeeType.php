<?php

namespace App\Enums;

enum FeeType: string
{
    case Tuition = 'tuition';
    case Transport = 'transport';
    case Library = 'library';
    case Exam = 'exam';
    case Others = 'others';

    public function label(): string
    {
        return match ($this) {
            self::Tuition => 'বেতন',
            self::Transport => 'পরিবহন',
            self::Library => 'গ্রন্থাগার',
            self::Exam => 'পরীক্ষা',
            self::Others => 'অন্যান্য',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Tuition => 'bg-blue-100 text-blue-800',
            self::Transport => 'bg-indigo-100 text-indigo-800',
            self::Library => 'bg-teal-100 text-teal-800',
            self::Exam => 'bg-purple-100 text-purple-800',
            self::Others => 'bg-gray-100 text-gray-800',
        };
    }
}
