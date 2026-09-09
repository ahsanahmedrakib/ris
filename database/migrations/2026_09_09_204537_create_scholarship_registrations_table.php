<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarship_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_no')->unique();
            $table->string('student_name');
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('union_name');
            $table->unsignedTinyInteger('class_no');
            $table->unsignedSmallInteger('serial_no');
            $table->string('roll_no', 20)->nullable();
            $table->string('mobile_no', 20);
            $table->string('bkash_no', 20);
            $table->string('status', 20)->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['class_no', 'serial_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarship_registrations');
    }
};
