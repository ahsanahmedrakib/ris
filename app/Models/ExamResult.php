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

    public static function calculateGrade(float|int $marks, float|int $total, float|int $passing): string
    {
        if ($marks < $passing) {
            return 'F';
        }

        $percentage = ($marks / max($total, 1)) * 100;

        return match (true) {
            $percentage >= 80 => 'A+',
            $percentage >= 70 => 'A',
            $percentage >= 60 => 'A-',
            $percentage >= 50 => 'B',
            $percentage >= 40 => 'C',
            default => 'D',
        };
    }
}
