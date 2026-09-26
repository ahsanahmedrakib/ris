<?php

namespace App\Support;

use Illuminate\Database\QueryException;

class UniqueConstraintViolation
{
    /**
     * Driver specific numeric codes for a unique key violation.
     * 1062 = MySQL/MariaDB ER_DUP_ENTRY, 19 = SQLite SQLITE_CONSTRAINT.
     *
     * @var list<int>
     */
    private const DRIVER_CODES = [1062, 19];

    /**
     * SQLSTATE class raised for every integrity constraint violation.
     */
    private const INTEGRITY_VIOLATION = '23000';

    /**
     * Detect a unique key violation without matching on the driver's English
     * error text, which varies between MySQL/MariaDB builds and locales and
     * therefore silently disables retry logic on shared cPanel hosts.
     */
    public static function matches(QueryException $exception): bool
    {
        if ((string) $exception->getCode() === self::INTEGRITY_VIOLATION) {
            return true;
        }

        return in_array((int) ($exception->errorInfo[1] ?? 0), self::DRIVER_CODES, true);
    }
}
