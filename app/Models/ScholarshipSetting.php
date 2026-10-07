<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\ScholarshipSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipSetting extends Model
{
    /** @use HasFactory<ScholarshipSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'is_open',
        'open_from',
        'open_to',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
            'open_from' => 'date',
            'open_to' => 'date',
        ];
    }

    /**
     * The single scholarship settings row, created "open" whenever it does not
     * exist yet.
     */
    public static function current(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            ['is_open' => true, 'open_from' => null, 'open_to' => null],
        );
    }

    /**
     * Whether the registration window admits public submissions at the given
     * moment: the toggle must be on and, when a date range is set, today must
     * fall inside it.
     */
    public function isOpenToday(?CarbonInterface $at = null): bool
    {
        $at ??= now();

        if (! $this->is_open) {
            return false;
        }

        if ($this->open_from && $at->lt($this->open_from)) {
            return false;
        }

        if ($this->open_to && $at->gt($this->open_to)) {
            return false;
        }

        return true;
    }

    /**
     * Whether the public registration window is currently open.
     */
    public static function isOpen(?CarbonInterface $at = null): bool
    {
        return static::current()->isOpenToday($at);
    }
}
