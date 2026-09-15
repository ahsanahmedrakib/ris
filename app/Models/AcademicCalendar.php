<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicCalendar extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'academic_calendars';

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'year',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getFileNameAttribute(): string
    {
        return basename($this->file_path);
    }
}
