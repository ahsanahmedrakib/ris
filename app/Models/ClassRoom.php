<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassRoom extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'section',
        'academic_year_id',
        'class_teacher_id',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('class-order', function (Builder $builder) {
            $builder->orderBy('sort_order')->orderBy('name');
        });

        static::creating(function (ClassRoom $class) {
            if (! $class->sort_order) {
                $class->sort_order = static::sortOrderForName($class->name);
            }
        });

        static::updating(function (ClassRoom $class) {
            if ($class->isDirty('name') && $class->sort_order === $class->getOriginal('sort_order')) {
                $class->sort_order = static::sortOrderForName($class->name);
            }
        });
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classTeacher()
    {
        return $this->belongsTo(User::class, 'class_teacher_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'class_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class, 'class_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'class_id');
    }

    public function classRoutines()
    {
        return $this->hasMany(ClassRoutine::class, 'class_id');
    }

    /**
     * Derive a serial order for a class name so the class list appears
     * as প্লে, নার্সারি, কেজি, ১ম, ২য়, ৩য়, ৪র্থ, ৫ম instead of alphabetical order.
     */
    public static function sortOrderForName(string $name): int
    {
        $normalized = strtolower(trim($name));

        $aliases = [
            10 => ['প্লে', 'play', 'preschool', 'pre-primary', 'pre primary', 'pp', 'p'],
            20 => ['নার্সারি', 'nursery', 'nur', 'n'],
            30 => ['কেজি', 'kg', 'kg-1', 'kg1', 'kg-2', 'kg2'],
            40 => ['১ম', '১য়', 'class-1', 'class 1', 'class one', '1st', '1'],
            50 => ['২য়', 'class-2', 'class 2', 'class two', '2nd', '2'],
            60 => ['৩য়', 'class-3', 'class 3', 'class three', '3rd', '3'],
            70 => ['৪র্থ', 'class-4', 'class 4', 'class four', '4th', '4'],
            80 => ['৫ম', 'class-5', 'class 5', 'class five', '5th', '5'],
            90 => ['৬ষ্ঠ', 'class-6', 'class 6', 'class six', '6'],
            100 => ['৭ম', 'class-7', 'class 7', 'class seven', '7'],
            110 => ['৮ম', 'class-8', 'class 8', 'class eight', '8'],
            120 => ['৯ম', 'class-9', 'class 9', 'class nine', '9'],
            130 => ['১০ম', 'class-10', 'class 10', 'class ten', '10'],
        ];

        foreach ($aliases as $order => $names) {
            if (in_array($normalized, $names, true)) {
                return $order;
            }
        }

        if (preg_match('/[০-৯]+/', $normalized, $matches)) {
            $digits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            $number = (int) str_replace($digits, range(0, 9), $matches[0]);

            return 40 + ($number - 1) * 10;
        }

        return 1000;
    }
}
