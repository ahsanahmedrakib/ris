<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use App\Support\UniqueConstraintViolation;
use Database\Factories\AdmissionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Admission extends Model
{
    /** @use HasFactory<AdmissionFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (Admission $admission) {
            if (blank($admission->pdf_token)) {
                $admission->pdf_token = Str::lower(Str::random(32));
            }
        });
    }

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
        'pdf_token',
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
            'present_address' => 'encrypted',
            'permanent_address' => 'encrypted',
            'legal_guardian_address' => 'encrypted',
            'local_guardian_address' => 'encrypted',
            'prev_school_address' => 'encrypted',
            'emergency_contact' => 'encrypted',
            'local_guardian_phone' => 'encrypted',
            'blood_group' => 'encrypted',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * The student this application produced, if it has already been admitted.
     * Both sides key on admission_no, which is unique on students.
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'admission_no', 'admission_no');
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

    /**
     * The year-and-class portion every admission_no for this class starts with,
     * e.g. 27-1 for first class in the 2027 session.
     */
    public static function referencePrefix(?string $classLevel): ?string
    {
        $classKey = static::classKey($classLevel);

        if ($classKey === null) {
            return null;
        }

        $academicYear = (int) (now()->format('n') >= 3 ? now()->addYear()->format('y') : now()->format('y'));

        return $academicYear.'-'.$classKey;
    }

    public static function nextAdmissionNo(?string $classLevel): ?string
    {
        $prefix = static::referencePrefix($classLevel);

        if ($prefix === null) {
            return null;
        }

        // The highest serial wins, not the newest row: ids and serials drift
        // apart after a retried insert or a hand edited admission_no, and
        // ordering by id would then hand back an already used number.
        // Soft deleted rows count too - the unique index still holds their
        // number, so reissuing one would fail the insert outright.
        $lastSerial = static::withTrashed()
            ->where('admission_no', 'like', $prefix.'%')
            ->pluck('admission_no')
            ->map(function (string $admissionNo) use ($prefix): int {
                preg_match('/^'.preg_quote($prefix, '/').'(\d{3})$/', $admissionNo, $matches);

                return isset($matches[1]) ? (int) $matches[1] : 0;
            })
            ->max() ?? 0;

        // Year, then class key, then a three digit serial with no separator
        // between them: 27-1001. The parser above expects exactly this shape.
        return sprintf('%s%03d', $prefix, $lastSerial + 1);
    }

    /**
     * Persist an admission whose admission_no is derived from a read-then-
     * increment serial, retrying when another writer claims the same number.
     *
     * Two applicants saving the same class at the same moment can pick the same
     * serial. The unique index rejects the loser; each retry re-reads the serial
     * which now includes the winner's row. The closure must therefore derive the
     * number afresh on every attempt rather than reuse an in-memory value.
     *
     * @param  callable(): Admission  $persist
     */
    public static function persistWithAdmissionNoRetry(callable $persist): ?Admission
    {
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                DB::beginTransaction();

                $admission = $persist();

                DB::commit();

                return $admission;
            } catch (QueryException $e) {
                DB::rollBack();

                if ($attempt < 3 && UniqueConstraintViolation::matches($e)) {
                    continue;
                }

                report($e);
            } catch (\Throwable $e) {
                DB::rollBack();

                report($e);
            }

            return null;
        }

        return null;
    }
}
