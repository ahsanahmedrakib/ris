<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_statistics', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('total_students');
            $table->unsignedInteger('total_teachers');
            $table->unsignedInteger('total_classes');
            $table->unsignedInteger('total_staff');
            $table->unsignedSmallInteger('founding_year');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_statistics');
    }
};
