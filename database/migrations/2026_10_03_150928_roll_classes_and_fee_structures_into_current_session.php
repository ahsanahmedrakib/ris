<?php

use App\Models\AcademicYear;
use App\Support\NumberConverter;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Rolls the live session over onto the current academic year.
 *
 * Classes and fee structures were entered against whichever session happened
 * to be selected in the admin dropdown, which left them spread across several
 * sessions. The public website only publishes the session flagged
 * `is_current`, so anything filed against an older session is invisible on
 * `/academic/fees` even though the admin listing shows it.
 *
 * The target session is resolved with a read-only lookup rather than
 * `AcademicYear::currentSession()`, because that helper creates the session row
 * when it is missing. A migration must not insert rows: on a fresh install
 * that would seed an academic year and defeat the lazy creation the admin
 * dropdown relies on. With nothing to merge, this migration does nothing.
 *
 * Rows are written through the query builder: only `academic_year_id` changes,
 * so no other column is touched.
 *
 * Idempotent: rows already on the current session are not selected, so an
 * interrupted run can simply be repeated.
 */
return new class extends Migration
{
    /**
     * Tables whose rows belong to whichever session is live.
     *
     * @var array<int, string>
     */
    private const SESSION_SCOPED = ['classes', 'fee_structures'];

    public function up(): void
    {
        $session = $this->findCurrentSession();

        if ($session === null) {
            return;
        }

        $stale = $this->staleSessionIds((int) NumberConverter::toAscii($session->name));

        if ($stale === []) {
            return;
        }

        $this->guardAgainstClassConflicts($stale, (int) $session->id);

        foreach (self::SESSION_SCOPED as $table) {
            DB::table($table)
                ->whereIn('academic_year_id', $stale)
                ->update(['academic_year_id' => $session->id]);
        }
    }

    /**
     * Run the migrations.
     */
    public function down(): void
    {
        // Not reversible: the original per-session split cannot be
        // reconstructed once the rows are merged.
    }

    /**
     * The session row for the current session year, or null when the school
     * has not got one yet. Read-only: never creates a session.
     */
    private function findCurrentSession(): ?object
    {
        $sessionYear = AcademicYear::sessionYear();

        foreach (DB::table('academic_years')->orderBy('id')->get() as $row) {
            if ($this->yearOf($row->name) === $sessionYear) {
                return $row;
            }
        }

        return null;
    }

    /**
     * A session is named with a single year; legacy rows carry a
     * "2025-2026" range, of which the opening year is the session.
     */
    private function yearOf(string $name): int
    {
        return (int) NumberConverter::toAscii(trim(explode('-', $name)[0]));
    }

    /**
     * Ids of sessions that ended before the current one began.
     *
     * @return array<int, int>
     */
    private function staleSessionIds(int $currentYear): array
    {
        $stale = [];

        foreach (DB::table('academic_years')->orderBy('id')->get() as $row) {
            $year = $this->yearOf($row->name);

            if ($year > 0 && $year < $currentYear) {
                $stale[] = (int) $row->id;
            }
        }

        return $stale;
    }

    /**
     * `classes` is unique on (name, section, academic_year_id), so merging is
     * only safe while no class name/section pair exists in both sessions.
     *
     * The pairs are compared as whole rows rather than plucked into a keyed
     * collection: keying by section would collapse every class in the school
     * onto a single key, since they all share one section.
     *
     * @param  array<int, int>  $stale
     */
    private function guardAgainstClassConflicts(array $stale, int $sessionId): void
    {
        $pair = fn ($class) => $class->name."\x00".$class->section;

        $incoming = DB::table('classes')
            ->whereIn('academic_year_id', $stale)
            ->get()
            ->map($pair)
            ->all();

        $existing = DB::table('classes')
            ->where('academic_year_id', $sessionId)
            ->get()
            ->map($pair)
            ->all();

        $clashes = array_values(array_unique(array_intersect($incoming, $existing)));

        if ($clashes !== []) {
            throw new RuntimeException(
                'Cannot merge classes into academic year '.$sessionId.': '
                .count($clashes).' class name/section pair(s) already exist there: '
                .collect($clashes)->map(fn ($pair) => str_replace("\x00", ' / ', $pair))->implode(', ')
            );
        }
    }
};
