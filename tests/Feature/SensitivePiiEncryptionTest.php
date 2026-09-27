<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Admission;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SensitivePiiEncryptionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function student_pii_is_encrypted_at_rest_but_readable_through_the_model(): void
    {
        $class = ClassRoom::create([
            'name' => 'Class Five',
            'section' => 'A',
            'academic_year_id' => AcademicYear::create([
                'name' => '2026',
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31',
                'is_current' => true,
            ])->id,
        ]);

        $student = Student::create([
            'user_id' => User::factory()->create(['role' => 'student'])->id,
            'admission_no' => 'ADM-26-9001',
            'class_id' => $class->id,
            'section' => 'A',
            'roll_no' => 1,
            'date_of_birth' => '2018-05-12',
            'gender' => 'male',
            'address' => 'গ্রামীণ রোড, ঢাকা',
            'guardian_name' => 'আব্দুল করিম',
            'guardian_phone' => '01712345679',
            'blood_group' => 'B+',
        ]);

        $raw = DB::table('students')->where('id', $student->id)->first();

        $this->assertNotSame('গ্রামীণ রোড, ঢাকা', $raw->address);
        $this->assertStringStartsWith('eyJpdiI6', $raw->address, 'The address column is not encrypted at rest.');
        $this->assertStringStartsWith('eyJpdiI6', $raw->blood_group, 'The blood group column is not encrypted at rest.');

        $student->refresh();

        $this->assertSame('গ্রামীণ রোড, ঢাকা', $student->address);
        $this->assertSame('B+', $student->blood_group);
    }

    #[Test]
    public function admission_pii_is_encrypted_at_rest_but_readable_through_the_model(): void
    {
        $admission = Admission::factory()->create([
            'present_address' => 'উপজেলা, গোপালগঞ্জ',
            'permanent_address' => 'জেলা, গোপালগঞ্জ',
            'emergency_contact' => '01854843814',
            'local_guardian_phone' => '01712345678',
            'blood_group' => 'A+',
        ]);

        $raw = DB::table('admissions')->where('id', $admission->id)->first();

        foreach (['present_address', 'permanent_address', 'emergency_contact', 'local_guardian_phone', 'blood_group'] as $column) {
            $this->assertStringStartsWith('eyJpdiI6', $raw->{$column}, "[{$column}] is not encrypted at rest.");
        }

        $admission->refresh();

        $this->assertSame('উপজেলা, গোপালগঞ্জ', $admission->present_address);
        $this->assertSame('জেলা, গোপালগঞ্জ', $admission->permanent_address);
        $this->assertSame('01854843814', $admission->emergency_contact);
        $this->assertSame('01712345678', $admission->local_guardian_phone);
        $this->assertSame('A+', $admission->blood_group);
    }

    #[Test]
    public function searchable_plaintext_columns_are_still_searchable(): void
    {
        Admission::factory()->create(['phone' => '01854843814']);

        $this->assertSame(1, Admission::where('phone', 'like', '%01854843814%')->count());
    }

    #[Test]
    public function updating_an_encrypted_field_replaces_the_ciphertext(): void
    {
        $admission = Admission::factory()->create(['present_address' => 'পুরানো ঠিকানা']);

        $admission->update(['present_address' => 'নতুন ঠিকানা']);

        $this->assertSame('নতুন ঠিকানা', $admission->fresh()->present_address);
        $this->assertStringStartsWith('eyJpdiI6', DB::table('admissions')->where('id', $admission->id)->value('present_address'));
    }

    #[Test]
    public function null_encrypted_fields_stay_null(): void
    {
        $admission = Admission::factory()->create([
            'present_address' => null,
            'blood_group' => null,
        ]);

        $raw = DB::table('admissions')->where('id', $admission->id)->first();

        $this->assertNull($raw->present_address);
        $this->assertNull($raw->blood_group);
        $this->assertNull($admission->fresh()->present_address);
    }
}
