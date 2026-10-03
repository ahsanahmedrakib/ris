<?php

use App\Support\NumberConverter;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Sessions are identified by a single year ("২০২৭"). Collapse legacy
     * hyphenated ranges ("২০২৫-২০২৬") down to the year they opened in.
     */
    public function up(): void
    {
        foreach (DB::table('academic_years')->orderBy('id')->get() as $row) {
            $label = trim(explode('-', $row->name)[0]);

            if ($label === '' || $row->name === $label) {
                continue;
            }

            $year = (int) NumberConverter::toAscii($label);

            if ($year === 0) {
                continue;
            }

            $normalized = (string) NumberConverter::toBangla((string) $year);

            $taken = DB::table('academic_years')
                ->where('name', $normalized)
                ->where('id', '!=', $row->id)
                ->exists();

            if ($taken) {
                continue;
            }

            DB::table('academic_years')->where('id', $row->id)->update(['name' => $normalized]);
        }
    }

    /**
     * Run the migrations.
     */
    public function down(): void
    {
        //
    }
};
