<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Slugs used to be derived from the title via a Bangla-to-English
        // glossary ("জাতীয় বিজ্ঞান মেলা" -> "national-science-fair"). That was
        // dropped in favour of a plain serial that an administrator can read
        // aloud ("notice-9", "campus-event-4"). Every existing row is rebuilt on
        // its model prefix plus primary key. The column is nullable and unique,
        // so the slug is cleared for the whole table first to avoid a transient
        // collision with a serial another row is about to take over.
        foreach (['notices' => 'notice', 'campus_events' => 'campus-event'] as $table => $prefix) {
            DB::table($table)->update(['slug' => null]);

            DB::table($table)
                ->orderBy('id')
                ->select('id')
                ->each(function ($row) use ($table, $prefix) {
                    DB::table($table)->where('id', $row->id)->update([
                        'slug' => $prefix.'-'.$row->id,
                    ]);
                });
        }
    }

    public function down(): void
    {
        // The old title-derived slugs were overwritten on every row, and the
        // Bangla-to-English translation that produced them no longer exists.
        // They are unrecoverable; rolling this back is intentionally a no-op.
    }
};
