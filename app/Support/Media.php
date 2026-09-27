<?php

namespace App\Support;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

/**
 * Typed access to the public upload disks.
 *
 * Storage::disk() is annotated as returning the Filesystem contract, which does
 * not declare url(). Resolving the disks here keeps that narrowing in one place
 * instead of scattering it across every call site.
 */
class Media
{
    public const IMAGES = 'public';

    public const FILES = 'public_files';

    public static function images(): FilesystemAdapter
    {
        return self::disk(self::IMAGES);
    }

    public static function files(): FilesystemAdapter
    {
        return self::disk(self::FILES);
    }

    public static function disk(string $name): FilesystemAdapter
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($name);

        return $disk;
    }
}
