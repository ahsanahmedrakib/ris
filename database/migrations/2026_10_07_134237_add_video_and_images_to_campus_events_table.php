<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('campus_events', function (Blueprint $table) {
            $table->text('video')->nullable()->after('image');
            $table->json('images')->nullable()->after('image');
        });

        // Existing rows only have the single cover column, so seed the gallery
        // from it to keep every consumer (cards, swiper, SEO) in sync.
        DB::table('campus_events')
            ->whereNotNull('image')
            ->whereNull('images')
            ->update(['images' => DB::raw('JSON_ARRAY(image)')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campus_events', function (Blueprint $table) {
            $table->dropColumn(['video', 'images']);
        });
    }
};
