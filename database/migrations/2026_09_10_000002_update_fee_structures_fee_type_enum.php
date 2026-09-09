<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->enum('fee_type', [
                'tuition', 'admission', 'exam', 'library', 'transport', 'lab', 'other', 'others',
            ])->change();
        });
    }

    public function down(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->enum('fee_type', ['tuition', 'admission', 'exam', 'library', 'transport', 'lab', 'other'])->change();
        });
    }
};
