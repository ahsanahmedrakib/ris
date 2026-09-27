<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Encrypts free-text/contact PII that is never queried, sorted or searched.
 *
 * Fields that ARE searched (admissions.phone/email, admissions.student_name_*,
 * students.admission_no/guardian_name/guardian_phone, users.email, ...) are
 * deliberately left in plaintext, because encrypting them would break the
 * `like` lookups and unique constraints those columns carry.
 */
return new class extends Migration
{
    /**
     * Columns widened to `text`, because a Laravel ciphertext payload does not
     * fit the original varchar length. Columns that are already `text` are
     * listed in ENCRYPT only.
     *
     * @var array<string, array<string, int>>
     */
    private const WIDEN = [
        'admissions' => [
            'blood_group' => 20,
            'legal_guardian_address' => 255,
            'local_guardian_address' => 255,
            'prev_school_address' => 255,
            'emergency_contact' => 20,
            'local_guardian_phone' => 20,
        ],
        'students' => [
            'blood_group' => 255,
        ],
    ];

    /** @var array<string, array<int, string>> */
    private const ENCRYPT = [
        'admissions' => [
            'present_address',
            'permanent_address',
            'legal_guardian_address',
            'local_guardian_address',
            'prev_school_address',
            'emergency_contact',
            'local_guardian_phone',
            'blood_group',
        ],
        'students' => [
            'address',
            'blood_group',
        ],
    ];

    public function up(): void
    {
        $this->widen();

        $this->rewrite(encrypt: true);
    }

    public function down(): void
    {
        $this->rewrite(encrypt: false);

        foreach (self::WIDEN as $table => $columns) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns) {
                foreach ($columns as $column => $length) {
                    $blueprint->string($column, $length)->nullable()->change();
                }
            });
        }
    }

    private function widen(): void
    {
        foreach (self::WIDEN as $table => $columns) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns) {
                foreach (array_keys($columns) as $column) {
                    $blueprint->text($column)->nullable()->change();
                }
            });
        }
    }

    /**
     * Converts every stored value between plaintext and ciphertext.
     *
     * This deliberately talks to the query builder rather than the model: a
     * `Model::save()` would call `getDirty()`, which casts the *original* value
     * and blows up with a DecryptException while rows are still plaintext.
     * Values are converted with Crypt directly and written raw.
     *
     * Idempotent: values that are already in the target shape are skipped, so
     * an interrupted run can simply be repeated.
     */
    private function rewrite(bool $encrypt): void
    {
        foreach (self::ENCRYPT as $table => $columns) {
            DB::table($table)
                ->select(array_merge(['id'], $columns))
                ->orderBy('id')
                ->chunkById(50, function ($rows) use ($table, $columns, $encrypt) {
                    foreach ($rows as $row) {
                        $updates = [];

                        foreach ($columns as $column) {
                            $value = $row->{$column};

                            if ($value === null || $value === '') {
                                continue;
                            }

                            if (self::isCiphertext($value) === $encrypt) {
                                continue;
                            }

                            $updates[$column] = $encrypt
                                ? Crypt::encryptString($value)
                                : Crypt::decryptString($value);
                        }

                        if ($updates !== []) {
                            DB::table($table)->where('id', $row->id)->update($updates);
                        }
                    }
                });
        }
    }

    /**
     * Laravel ciphertext is base64-encoded JSON, so every payload starts with
     * the base64 of `{"iv":`.
     */
    private static function isCiphertext(string $value): bool
    {
        return str_starts_with($value, 'eyJpdiI6');
    }
};
