<?php

namespace App\Models;

use Database\Factories\VisitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    /** @use HasFactory<VisitFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'visitor_id',
        'ip_address',
        'user_agent',
        'url',
        'visited_at',
    ];

    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
        ];
    }

    public static function todayUnique(): int
    {
        return static::whereDate('visited_at', today())
            ->distinct()
            ->count('ip_address');
    }

    public static function totalUnique(): int
    {
        return static::distinct()
            ->count('ip_address');
    }

    public static function toBengali(int $number): string
    {
        return strtr((string) $number, [
            '0' => '০',
            '1' => '১',
            '2' => '২',
            '3' => '৩',
            '4' => '৪',
            '5' => '৫',
            '6' => '৬',
            '7' => '৭',
            '8' => '৮',
            '9' => '৯',
        ]);
    }
}
