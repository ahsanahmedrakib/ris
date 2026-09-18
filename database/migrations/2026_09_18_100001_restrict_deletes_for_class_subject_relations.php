<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->foreign('class_id')->references('id')->on('classes')->restrictOnDelete();
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->foreign('class_id')->references('id')->on('classes')->restrictOnDelete();
        });

        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->foreign('subject_id')->references('id')->on('subjects')->restrictOnDelete();

            $table->dropForeign(['exam_id']);
            $table->foreign('exam_id')->references('id')->on('exams')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
        });

        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();

            $table->dropForeign(['exam_id']);
            $table->foreign('exam_id')->references('id')->on('exams')->cascadeOnDelete();
        });
    }
};
