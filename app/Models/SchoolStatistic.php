<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolStatistic extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'school_statistics';

    protected $fillable = [
        'total_students',
        'total_teachers',
        'total_classes',
        'total_staff',
        'founding_year',
    ];

    protected function casts(): array
    {
        return [
            'total_students' => 'integer',
            'total_teachers' => 'integer',
            'total_classes' => 'integer',
            'total_staff' => 'integer',
            'founding_year' => 'integer',
        ];
    }

    /**
     * Default "school at a glance" values shown on the public website
     * when no record exists in the database.
     *
     * @return array{total_students: int, total_teachers: int, total_classes: int, total_staff: int, founding_year: int}
     */
    public static function defaults(): array
    {
        return [
            'total_students' => 500,
            'total_teachers' => 20,
            'total_classes' => 8,
            'total_staff' => 10,
            'founding_year' => 2015,
        ];
    }

    /**
     * Years of experience derived from the founding year.
     */
    public function experienceYears(): int
    {
        return max((int) date('Y') - $this->founding_year, 1);
    }
}
