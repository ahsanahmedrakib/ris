<?php

namespace App\Enums;

enum DayOfWeek: string
{
    case Saturday = 'saturday';
    case Sunday = 'sunday';
    case Monday = 'monday';
    case Tuesday = 'tuesday';
    case Wednesday = 'wednesday';
    case Thursday = 'thursday';
    case Friday = 'friday';

    public function label(): string
    {
        return match ($this) {
            self::Saturday => 'শনিবার',
            self::Sunday => 'রবিবার',
            self::Monday => 'সোমবার',
            self::Tuesday => 'মঙ্গলবার',
            self::Wednesday => 'বুধবার',
            self::Thursday => 'বৃহস্পতিবার',
            self::Friday => 'শুক্রবার',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Saturday => 'bg-red-100 text-red-800',
            self::Sunday => 'bg-orange-100 text-orange-800',
            self::Monday => 'bg-yellow-100 text-yellow-800',
            self::Tuesday => 'bg-green-100 text-green-800',
            self::Wednesday => 'bg-blue-100 text-blue-800',
            self::Thursday => 'bg-indigo-100 text-indigo-800',
            self::Friday => 'bg-purple-100 text-purple-800',
        };
    }
}
