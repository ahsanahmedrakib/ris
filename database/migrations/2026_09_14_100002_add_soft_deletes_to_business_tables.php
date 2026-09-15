<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Tables that should support soft deletes. */
    private const TABLES = [
        'admissions',
        'attendance',
        'book_borrowings',
        'books',
        'bus_routes',
        'buses',
        'class_routines',
        'classes',
        'contact_messages',
        'exam_results',
        'exams',
        'fee_invoices',
        'fee_payments',
        'fee_structures',
        'leave_requests',
        'notices',
        'payroll',
        'scholarship_registrations',
        'staff',
        'student_parents',
        'student_transport',
        'subjects',
        'teacher_profiles',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->softDeletes();
            });
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('deleted_at');
            });
        }
    }
};
