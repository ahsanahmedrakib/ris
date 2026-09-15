<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->string('visitor_id', 45)->change();
        });

        DB::table('visits')->delete();
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->uuid('visitor_id')->change();
        });
    }
};
