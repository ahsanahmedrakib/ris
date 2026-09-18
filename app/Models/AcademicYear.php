<?php

namespace App\Models;

use App\Support\NumberConverter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function classes()
    {
        return $this->hasMany(ClassRoom::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function yearLabel(): string
    {
        return trim(explode('-', $this->name)[0]);
    }

    public static function sessionYear(?\DateTimeInterface $date = null): int
    {
        $date ??= now();

        return $date->month >= 12 ? $date->year + 1 : $date->year;
    }

    /**
     * Ordered academic years for dropdowns, always including the current
     * session year (current calendar year Jan-Nov, next year in December).
     */
    public static function forSessionDropdown(): Collection
    {
        $years = static::orderByDesc('is_current')->orderByDesc('name')->get();

        $sessionYear = static::sessionYear();

        $exists = $years->contains(
            fn (self $year) => (int) NumberConverter::toAscii($year->yearLabel()) === $sessionYear
        );

        if (! $exists) {
            $years->prepend(static::create([
                'name' => (string) NumberConverter::toBangla($sessionYear.'-'.($sessionYear + 1)),
                'start_date' => $sessionYear.'-01-01',
                'end_date' => $sessionYear.'-12-31',
                'is_current' => false,
            ]));
        }

        return $years;
    }
}
