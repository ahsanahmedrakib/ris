<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use App\Enums\DayOfWeek;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassRoutine extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'class_routines';

    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room_no',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
        ];
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Bangla label for the stored integer `day_of_week`, or the enum value
     * when a legacy string was persisted.
     */
    public function dayLabel(): ?string
    {
        return $this->dayEnum()?->label();
    }

    public function dayEnum(): ?DayOfWeek
    {
        if (is_numeric($this->day_of_week)) {
            return DayOfWeek::tryFromOrder((int) $this->day_of_week);
        }

        return DayOfWeek::tryFrom((string) $this->day_of_week);
    }
}
