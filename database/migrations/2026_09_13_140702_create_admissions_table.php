<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->string('admission_no')->unique();
            $table->string('status', 20)->default('pending');

            $table->string('academic_year', 10)->nullable();
            $table->string('roll_no', 20)->nullable();
            $table->string('section', 50)->nullable();
            $table->json('batch')->nullable();
            $table->date('admission_date')->nullable();
            $table->date('form_collect_date')->nullable();
            $table->date('form_submit_date')->nullable();
            $table->json('class_level')->nullable();

            $table->string('student_name_bn');
            $table->string('student_name_en', 255)->nullable();
            $table->date('dob')->nullable();
            $table->string('age', 20)->nullable();
            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();
            $table->string('blood_group', 20)->nullable();

            $table->string('father_name_bn')->nullable();
            $table->string('father_name_en', 255)->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('mother_name_bn')->nullable();
            $table->string('mother_name_en', 255)->nullable();
            $table->string('mother_occupation')->nullable();

            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('emergency_contact', 20)->nullable();

            $table->string('legal_guardian_name')->nullable();
            $table->string('legal_guardian_occupation')->nullable();
            $table->string('legal_guardian_relation')->nullable();
            $table->string('legal_guardian_address')->nullable();

            $table->string('local_guardian_name')->nullable();
            $table->string('local_guardian_occupation')->nullable();
            $table->string('local_guardian_relation')->nullable();
            $table->string('local_guardian_address')->nullable();
            $table->string('local_guardian_phone', 20)->nullable();

            $table->string('prev_school_name')->nullable();
            $table->string('prev_school_address')->nullable();
            $table->string('prev_roll_no', 20)->nullable();
            $table->string('prev_marks')->nullable();

            $table->string('reference')->nullable();
            $table->string('reference_phone', 20)->nullable();
            $table->string('reference_sign')->nullable();

            $table->string('student_photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
