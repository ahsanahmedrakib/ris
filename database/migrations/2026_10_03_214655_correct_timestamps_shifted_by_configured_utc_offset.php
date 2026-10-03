<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Corrects every stored instant that drifted while the app timezone was UTC.
 *
 * `config/app.php` used to pin PHP to UTC while the MySQL session zone was the
 * server's own Asia/Dhaka. MySQL TIMESTAMP columns convert on write and again
 * on read, so a moment recorded as 20:52 local was written as "14:52", stored
 * six hours early, read back as "14:52" and finally rendered as 14:52 - six
 * hours before the time it actually records.
 *
 * Both ends are now pinned to Asia/Dhaka (config/app.php and
 * config/database.php), so new rows are correct. This adds the six hours back
 * to the ones already on disk.
 *
 * Only TIMESTAMP columns are touched. DATE and TIME columns hold calendar
 * values rather than instants - a date of birth, a class start time - and
 * carry no offset, so they are deliberately left alone.
 */
return new class extends Migration
{
    /**
     * Hours to add back to each stored instant.
     *
     * Hardcoded rather than read from config so the correction stays fixed to
     * what actually happened, even if the timezone is reconfigured later.
     */
    private const HOURS_TO_RESTORE = 6;

    public function up(): void
    {
        if (! $this->convertsTimestamps()) {
            return;
        }

        // Pin the session zone so the arithmetic does not depend on whatever
        // zone the database server happens to be configured with.
        DB::statement("SET time_zone = '+06:00'");

        foreach ($this->timestampColumns() as $table => $columns) {
            foreach ($columns as $column) {
                DB::table($table)->whereNotNull($column)->update([
                    $column => DB::raw("{$column} + INTERVAL ".self::HOURS_TO_RESTORE.' HOUR'),
                ]);
            }
        }
    }

    /**
     * Only MySQL-family connections store TIMESTAMP columns as UTC and convert
     * them against the session zone. SQLite keeps whatever it is handed, so it
     * never drifted and there is nothing to correct - and it understands
     * neither `SET time_zone` nor `information_schema`.
     */
    private function convertsTimestamps(): bool
    {
        return in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true);
    }

    /**
     * Run the migrations.
     */
    public function down(): void
    {
        // Not reversible: the original instants cannot be recovered, and
        // running this again would push every row a further six hours out.
    }

    /**
     * Every TIMESTAMP column, grouped by table.
     *
     * @return array<string, array<int, string>>
     */
    private function timestampColumns(): array
    {
        $columns = DB::select(
            'SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND DATA_TYPE = "timestamp"
             ORDER BY TABLE_NAME, COLUMN_NAME'
        );

        $grouped = [];

        foreach ($columns as $column) {
            $grouped[$column->TABLE_NAME][] = $column->COLUMN_NAME;
        }

        return $grouped;
    }
};
