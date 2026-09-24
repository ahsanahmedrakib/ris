<?php

use App\Models\ScholarshipRegistration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scholarship_registrations', function (Blueprint $table) {
            $table->string('pdf_token', 40)->nullable()->after('mobile_no')->unique();
        });

        // Backfill tokens for existing registrations so their admit links keep working.
        ScholarshipRegistration::withTrashed()
            ->whereNull('pdf_token')
            ->select('id')
            ->get()
            ->each(function ($registration) {
                $registration->timestamps = false;
                $registration->updateQuietly(['pdf_token' => Str::random(32)]);
            });
    }

    public function down(): void
    {
        Schema::table('scholarship_registrations', function (Blueprint $table) {
            $table->dropColumn('pdf_token');
        });
    }
};
