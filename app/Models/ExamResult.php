<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamResult extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'exam_results';

    protected $fillable = [
        'exam_id',
        'student_id',
        'subject_id',
        'marks_obtained',
        'grade',
        'remarks',
        'entered_by',
    ];

    protected function casts(): array
    {
        return [
            'marks_obtained' => 'decimal:2',
        ];
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function marker()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }
}
