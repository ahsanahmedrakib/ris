<?php

use App\Models\Admission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admissions', function (Blueprint $table) {
            $table->string('pdf_token', 40)->nullable()->after('admission_no')->unique();
        });

        // Backfill so any admission that predates this column still resolves a
        // downloadable form. Admissions created without a number keep one here
        // because the public PDF link is keyed on the token, not the number.
        Admission::withTrashed()
            ->whereNull('pdf_token')
            ->select('id')
            ->get()
            ->each(function ($admission) {
                $admission->timestamps = false;
                $admission->updateQuietly(['pdf_token' => Str::random(32)]);
            });
    }

    public function down(): void
    {
        Schema::table('admissions', function (Blueprint $table) {
            $table->dropColumn('pdf_token');
        });
    }
};
