<?php

namespace App\Support;

class NumberConverter
{
    private const BANGLA_DIGITS = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

    /**
     * Convert Bangla (and Arabic-Indic) numerals to ASCII digits so numeric
     * validation like "integer" works when users type ১০০ instead of 100.
     */
    public static function toAscii(string|int|null $value): string|int|null
    {
        if ($value === null || is_int($value)) {
            return $value;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return $value;
        }

        return str_replace(self::BANGLA_DIGITS, range(0, 9), $value);
    }

    /**
     * Convert ASCII digits to Bangla numerals.
     */
    public static function toBangla(string|int|null $value): string|int|null
    {
        if ($value === null || is_int($value)) {
            return $value;
        }

        return str_replace(range(0, 9), self::BANGLA_DIGITS, (string) $value);
    }
}
