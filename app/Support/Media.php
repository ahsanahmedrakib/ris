<?php

namespace App\Support;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

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

    /**
     * Path columns storing these values are varchar(255), so a stored path is
     * never allowed to grow past what the schema can hold.
     */
    private const MAX_PATH_LENGTH = 255;

    /**
     * Extensions that must never be written to a public folder, even when a
     * client names a file that way. The upload folders also refuse to execute
     * anything, so this is a second layer rather than the only one.
     *
     * @var list<string>
     */
    private const DANGEROUS_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phps', 'phtml', 'phar',
        'cgi', 'pl', 'py', 'rb', 'sh', 'bash', 'exe', 'bat', 'cmd', 'com',
        'htaccess', 'htpasswd', 'ini', 'jsp', 'asp', 'aspx', 'svgz',
    ];

    public static function images(): FilesystemAdapter
    {
        return self::disk(self::IMAGES);
    }

    public static function files(): FilesystemAdapter
    {
        return self::disk(self::FILES);
    }

    /**
     * Store an upload under its original file name inside the images folder.
     */
    public static function storeImage(UploadedFile $file, string $folder): string
    {
        return self::storeNamed($file, $folder, self::IMAGES);
    }

    /**
     * Store an upload under its original file name inside the files folder.
     */
    public static function storeFile(UploadedFile $file, string $folder): string
    {
        return self::storeNamed($file, $folder, self::FILES);
    }

    /**
     * Store an upload under its original file name, without overwriting.
     *
     * The stored path keeps the uploaded name because staff and applicants
     * recognise their own files, but a client supplied name is untrusted input:
     * Symfony's getClientOriginalName() hands it back verbatim, so
     * `../../.htaccess` arrives intact. Everything a name is allowed to contain
     * is therefore decided here instead of being passed to the filesystem.
     */
    public static function storeNamed(UploadedFile $file, string $folder, string $disk): string
    {
        $folder = trim(str_replace('\\', '/', $folder), '/');
        $storage = self::disk($disk);

        $stem = self::stem($file, $folder);
        $extension = self::extension($file);

        // A name already in use must not overwrite an existing upload, so a
        // short suffix is added until the name is free.
        $name = $stem.'.'.$extension;
        $attempt = 0;

        while ($storage->exists($folder === '' ? $name : $folder.'/'.$name)) {
            $attempt++;
            $name = $stem.'-'.Str::lower(Str::random(6)).'.'.$extension;
        }

        $stored = $storage->putFileAs($folder, $file, $name);

        if ($stored === false) {
            throw new RuntimeException("Unable to store the upload as [{$folder}/{$name}].");
        }

        return $stored;
    }

    /**
     * The readable part of the name: safe to place on disk, and short enough
     * that the full path still fits its column.
     */
    private static function stem(UploadedFile $file, string $folder): string
    {
        $original = basename(str_replace('\\', '/', $file->getClientOriginalName()));
        $extension = self::extension($file);

        $original = str_ends_with(strtolower($original), '.'.strtolower($extension))
            ? substr($original, 0, -(strlen($extension) + 1))
            : pathinfo($original, PATHINFO_FILENAME);

        // Keep letters, combining marks (Bangla vowel signs are marks rather
        // than letters and would otherwise be stripped), digits, spaces and the
        // separators people use in names. Everything else could steer the write
        // somewhere unintended.
        $clean = preg_replace('/[^\p{L}\p{N}\p{M}\s._-]+/u', '-', $original) ?? '';
        $clean = trim(preg_replace('/\s+/u', ' ', $clean) ?? '', ' .-');

        if ($clean === '') {
            $clean = 'file';
        }

        return self::fitToColumn($clean, $folder, $extension);
    }

    /**
     * Shorten the name until the whole path fits its column.
     *
     * The budget is counted in bytes, which is the stricter of the two limits:
     * a multibyte name has more bytes than characters, so a byte budget keeps
     * the path valid under both a character limited and a byte limited varchar.
     * The spare characters leave room for the `-xxxxxx` collision suffix.
     */
    private static function fitToColumn(string $stem, string $folder, string $extension): string
    {
        $prefix = $folder === '' ? '' : $folder.'/';
        $budget = self::MAX_PATH_LENGTH - strlen($prefix) - strlen($extension) - 8;

        while ($stem !== '' && strlen($prefix.$stem.'.'.$extension) > $budget) {
            $stem = mb_substr($stem, 0, mb_strlen($stem) - 1);
        }

        return $stem === '' ? 'file' : $stem;
    }

    /**
     * The extension to store with the file.
     *
     * An extension the client sent is not trustworthy, so one that is malformed
     * or executable is replaced with an extension derived from the detected MIME
     * type of the actual bytes. That guess is re-checked as well, because Symfony
     * derives part of it from the file path and would otherwise hand back the
     * same executable extension.
     */
    private static function extension(UploadedFile $file): string
    {
        $sent = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));

        if (self::isSafeExtension($sent)) {
            return $sent;
        }

        $guessed = strtolower($file->guessExtension() ?: '');

        return self::isSafeExtension($guessed) ? $guessed : 'bin';
    }

    private static function isSafeExtension(string $extension): bool
    {
        return $extension !== ''
            && preg_match('/^[a-z0-9]{1,10}$/', $extension) === 1
            && ! in_array($extension, self::DANGEROUS_EXTENSIONS, true);
    }

    public static function disk(string $name): FilesystemAdapter
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($name);

        return $disk;
    }
}
