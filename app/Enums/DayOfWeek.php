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

    /**
     * Numeric order matching the integer `day_of_week` column.
     * Friday is excluded from the routine schedule.
     */
    public function order(): int
    {
        return match ($this) {
            self::Saturday => 1,
            self::Sunday => 2,
            self::Monday => 3,
            self::Tuesday => 4,
            self::Wednesday => 5,
            self::Thursday => 6,
            self::Friday => 7,
        };
    }

    public static function tryFromOrder(int $order): ?self
    {
        return match ($order) {
            1 => self::Saturday,
            2 => self::Sunday,
            3 => self::Monday,
            4 => self::Tuesday,
            5 => self::Wednesday,
            6 => self::Thursday,
            7 => self::Friday,
            default => null,
        };
    }

    /**
     * Routine days (excluding Friday), ordered by the working week.
     *
     * @return array<int, self>
     */
    public static function weekdays(): array
    {
        return [
            self::Saturday,
            self::Sunday,
            self::Monday,
            self::Tuesday,
            self::Wednesday,
            self::Thursday,
        ];
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
