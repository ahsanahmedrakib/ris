<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const SERIAL_ORDER = [
        'প্লে' => 10,
        'নার্সারি' => 20,
        'কেজি' => 30,
        '১ম' => 40,
        '২য়' => 50,
        '৩য়' => 60,
        '৪র্থ' => 70,
        '৫ম' => 80,
    ];

    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('section');
            $table->index('sort_order');
        });

        DB::table('classes')->orderBy('id')->get()->each(function ($class) {
            DB::table('classes')
                ->where('id', $class->id)
                ->update(['sort_order' => self::SERIAL_ORDER[$class->name] ?? 1000]);
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
            $table->dropColumn('sort_order');
        });
    }
};
