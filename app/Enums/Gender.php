<?php

namespace App\Enums;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Male => 'পুরুষ',
            self::Female => 'মহিলা',
            self::Other => 'অন্যান্য',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Male => 'bg-blue-100 text-blue-800',
            self::Female => 'bg-pink-100 text-pink-800',
            self::Other => 'bg-gray-100 text-gray-800',
        };
    }
}
