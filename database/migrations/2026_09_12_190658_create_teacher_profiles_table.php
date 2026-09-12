<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('photo')->nullable();
            $table->string('designation')->nullable();
            $table->string('subject')->nullable();
            $table->string('qualification')->nullable();
            $table->string('institute')->nullable();
            $table->text('previous_institutions')->nullable();
            $table->date('joining_date')->nullable();
            $table->text('bio')->nullable();
            $table->text('experience')->nullable();
            $table->text('achievements')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_profiles');
    }
};
