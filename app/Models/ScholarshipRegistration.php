<?php

namespace App\Models;

use App\Core\Traits\LogsActivity;
use Database\Factories\ScholarshipRegistrationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ScholarshipRegistration extends Model
{
    /** @use HasFactory<ScholarshipRegistrationFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    public const CLASSES = [
        1 => 'প্রথম',
        2 => 'দ্বিতীয়',
        3 => 'তৃতীয়',
        4 => 'চতুর্থ',
        5 => 'পঞ্চম',
    ];

    public const BKASH_NUMBER = '01618197972';

    protected $fillable = [
        'registration_no',
        'student_name',
        'father_name',
        'mother_name',
        'school_name',
        'class_no',
        'serial_no',
        'roll_no',
        'mobile_no',
        'pdf_token',
        'bkash_no',
        'payment_method',
        'status',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted(): void
    {
        static::creating(function (ScholarshipRegistration $registration) {
            if (blank($registration->pdf_token)) {
                $registration->pdf_token = Str::lower(Str::random(32));
            }
        });
    }

    public static function nextRegistrationNo(int $classNo): string
    {
        // Include soft-deleted so we never reuse a registration_no
        $lastSerial = static::withTrashed()
            ->whereYear('created_at', now()->year)
            ->where('class_no', $classNo)
            ->orderByDesc('serial_no')
            ->value('serial_no');

        $serial = ((int) $lastSerial) + 1;

        return sprintf('%s-%d%03d', now()->format('y'), $classNo, $serial);
    }
}
