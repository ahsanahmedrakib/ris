<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use Database\Factories\AdmissionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admission extends Model
{
    /** @use HasFactory<AdmissionFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    public const STATUSES = [
        'pending' => 'পেন্ডিং',
        'approved' => 'অনুমোদিত',
        'rejected' => 'প্রত্যাখ্যাত',
    ];

    public const BATCHES = ['প্রভাতী', 'দিবা'];

    public const CLASS_OPTIONS = ['প্লে', 'নার্সারি', 'কেজি', '১ম', '২য়', '৩য়', '৪র্থ', '৫ম'];

    /** @var array<string, string> Class label → admission-number leading key. */
    public const CLASS_KEYS = [
        'প্লে' => 'P',
        'নার্সারি' => 'N',
        'কেজি' => 'K',
        '১ম শ্রেণি' => '1',
        '১ম' => '1',
        '২য় শ্রেণি' => '2',
        '২য়' => '2',
        '৩য় শ্রেণি' => '3',
        '৩য়' => '3',
        '৪র্থ শ্রেণি' => '4',
        '৪র্থ' => '4',
        '৫ম শ্রেণি' => '5',
        '৫ম' => '5',
    ];

    protected $fillable = [
        'admission_no',
        'status',
        'academic_year',
        'roll_no',
        'section',
        'batch',
        'admission_date',
        'form_collect_date',
        'form_submit_date',
        'class_level',
        'student_name_bn',
        'student_name_en',
        'dob',
        'age',
        'nationality',
        'religion',
        'blood_group',
        'father_name_bn',
        'father_name_en',
        'father_occupation',
        'mother_name_bn',
        'mother_name_en',
        'mother_occupation',
        'present_address',
        'permanent_address',
        'phone',
        'email',
        'emergency_contact',
        'legal_guardian_name',
        'legal_guardian_occupation',
        'legal_guardian_relation',
        'legal_guardian_address',
        'local_guardian_name',
        'local_guardian_occupation',
        'local_guardian_relation',
        'local_guardian_address',
        'local_guardian_phone',
        'prev_school_name',
        'prev_school_address',
        'prev_roll_no',
        'prev_marks',
        'reference',
        'reference_phone',
        'reference_sign',
        'student_photo',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'batch' => 'array',
            'class_level' => 'array',
            'dob' => 'date',
            'admission_date' => 'date',
            'form_collect_date' => 'date',
            'form_submit_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getBatchLabelAttribute(): string
    {
        return $this->batch ? implode(', ', $this->batch) : '-';
    }

    public function getClassLabelAttribute(): string
    {
        return $this->class_level ? implode(', ', $this->class_level) : '-';
    }

    public static function classKey(?string $classLevel): ?string
    {
        $classLevel = trim((string) $classLevel);

        if ($classLevel === '') {
            return null;
        }

        return self::CLASS_KEYS[$classLevel] ?? null;
    }

    public static function nextAdmissionNo(?string $classLevel): ?string
    {
        $classKey = static::classKey($classLevel);

        if ($classKey === null) {
            return null;
        }

        $academicYear = (int) (now()->format('n') >= 3 ? now()->addYear()->format('y') : now()->format('y'));
        $prefix = $academicYear.'-'.$classKey;

        $lastSerial = static::where('admission_no', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('admission_no');

        $serial = 1;

        if ($lastSerial && preg_match('/^'.preg_quote($prefix, '/').'(\d{3})$/', (string) $lastSerial, $m)) {
            $serial = (int) $m[1] + 1;
        }

        return sprintf('%s-%s%03d', $academicYear, $classKey, $serial);
    }
}
