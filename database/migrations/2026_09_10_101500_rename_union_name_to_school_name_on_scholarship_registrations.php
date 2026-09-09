<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scholarship_registrations', function (Blueprint $table) {
            $table->renameColumn('union_name', 'school_name');
        });
    }

    public function down(): void
    {
        Schema::table('scholarship_registrations', function (Blueprint $table) {
            $table->renameColumn('school_name', 'union_name');
        });
    }
};
