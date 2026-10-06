<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('campus_news', 'campus_events');

        // Fallback slugs that were built from the old prefix (a title with no
        // slugifiable content becomes "{prefix}-{id}") follow the rename so the
        // addresses that were already shared are regenerated as their target.
        DB::table('campus_events')
            ->where('slug', 'like', 'campus-life-%')
            ->get(['id', 'slug'])
            ->each(function ($row) {
                $new = preg_replace('/^campus-life-/', 'campus-events-', (string) $row->slug);

                if ($new === null || $new === $row->slug) {
                    return;
                }

                DB::table('campus_events')->where('id', $row->id)->update(['slug' => $new]);
            });
    }

    public function down(): void
    {
        DB::table('campus_events')
            ->where('slug', 'like', 'campus-events-%')
            ->get(['id', 'slug'])
            ->each(function ($row) {
                $old = preg_replace('/^campus-events-/', 'campus-life-', (string) $row->slug);

                if ($old === null || $old === $row->slug) {
                    return;
                }

                DB::table('campus_events')->where('id', $row->id)->update(['slug' => $old]);
            });

        Schema::rename('campus_events', 'campus_news');
    }
};
