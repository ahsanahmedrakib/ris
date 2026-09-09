<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Teacher = 'teacher';
    case Parent = 'parent';
    case Student = 'student';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'প্রশাসক',
            self::Teacher => 'শিক্ষক',
            self::Parent => 'অভিভাবক',
            self::Student => 'ছাত্র/ছাত্রী',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Admin => 'bg-red-100 text-red-800',
            self::Teacher => 'bg-blue-100 text-blue-800',
            self::Parent => 'bg-green-100 text-green-800',
            self::Student => 'bg-purple-100 text-purple-800',
        };
    }
}
