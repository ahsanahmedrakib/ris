<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
        });

        Schema::table('campus_news', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
        });

        // The rows that already exist get the same treatment a new record gets,
        // so nothing is left unroutable and no admin has to edit them by hand.
        // They are read in batches because the backfill is a query per row, and
        // the slug column is unique, so transliterated titles that land on the
        // same word have to be told apart by a suffix.
        foreach (['notices' => 'notice', 'campus_news' => 'campus-life'] as $table => $prefix) {
            $assigned = [];

            DB::table($table)->orderBy('id')->select(['id', 'title'])->chunkById(200, function ($rows) use ($table, $prefix, &$assigned) {
                foreach ($rows as $row) {
                    DB::table($table)->where('id', $row->id)->update([
                        'slug' => $this->slugFor($table, $prefix, (string) $row->title, $row->id, $assigned),
                    ]);
                }
            }, 'id');
        }
    }

    /**
     * The transliterated title, suffixed until it is free, or the prefix and the
     * primary key when the title has nothing in it that can be transliterated.
     *
     * @param  list<string>  $assigned
     */
    private function slugFor(string $table, string $prefix, string $title, int $id, array &$assigned): string
    {
        $base = Str::slug($title);

        if ($base === '') {
            return $prefix.'-'.$id;
        }

        $slug = $base;
        $suffix = 1;

        while (in_array($slug, $assigned, true) || DB::table($table)->where('slug', $slug)->exists()) {
            $suffix++;
            $slug = $base.'-'.$suffix;
        }

        $assigned[] = $slug;

        return $slug;
    }

    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });

        Schema::table('campus_news', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
