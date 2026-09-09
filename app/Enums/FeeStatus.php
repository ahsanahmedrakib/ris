<?php

namespace App\Enums;

enum FeeStatus: string
{
    case Pending = 'pending';
    case Partial = 'partial';
    case Paid = 'paid';
    case Overdue = 'overdue';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'অপেক্ষমাণ',
            self::Partial => 'আংশিক',
            self::Paid => 'পরিশোধিত',
            self::Overdue => 'বকেয়া',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'bg-yellow-100 text-yellow-800',
            self::Partial => 'bg-orange-100 text-orange-800',
            self::Paid => 'bg-green-100 text-green-800',
            self::Overdue => 'bg-red-100 text-red-800',
        };
    }
}
