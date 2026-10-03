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
            'is_current' => 'boolean',
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

    /**
     * A session is identified by a single year ("২০২৭"), never a range.
     */
    public function yearLabel(): string
    {
        return trim(explode('-', $this->name)[0]);
    }

    public static function year(int $year): string
    {
        return (string) NumberConverter::toBangla((string) $year);
    }

    /**
     * The session rolls over in March, so the current session is the current
     * calendar year through January and February, and the next calendar year
     * from March through December.
     */
    public static function sessionYear(?\DateTimeInterface $date = null): int
    {
        $date ??= now();

        return $date->month >= 3 ? $date->year + 1 : $date->year;
    }

    /**
     * Resolve the session row for the current session year, creating it when
     * missing. The session row is the only row flagged as current, which is
     * what the public website publishes.
     */
    public static function currentSession(): self
    {
        $sessionYear = static::sessionYear();

        $session = static::query()
            ->get()
            ->first(fn (self $year) => (int) NumberConverter::toAscii($year->yearLabel()) === $sessionYear);

        if (! $session) {
            $session = static::create([
                'name' => static::year($sessionYear),
                'start_date' => $sessionYear.'-01-01',
                'end_date' => $sessionYear.'-12-31',
                'is_current' => true,
            ]);
        }

        static::query()
            ->where('is_current', true)
            ->whereKeyNot($session->getKey())
            ->update(['is_current' => false]);

        if (! $session->is_current) {
            $session->forceFill(['is_current' => true])->save();
        }

        return $session;
    }

    /**
     * Ordered academic years for dropdowns, always including the current
     * session first, then remaining sessions newest to oldest.
     */
    public static function forSessionDropdown(): Collection
    {
        static::currentSession();

        return static::query()
            ->get()
            ->sortByDesc(fn (self $year) => $year->is_current
                ? PHP_INT_MAX
                : (int) NumberConverter::toAscii($year->yearLabel()))
            ->values();
    }
}
