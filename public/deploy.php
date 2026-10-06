<?php

/**
 * One-off deployment helper for shared hosting without SSH access.
 *
 * Change the token below, upload this file to your application's public
 * directory (the one that serves as the document root), then visit:
 *
 *     https://ris.edu.bd/deploy.php?token=YOUR_CHANGED_TOKEN
 *
 * It runs pending database migrations, rebuilds the application caches so the
 * new routes/views/config take effect, and finally deletes itself. It cannot
 * be reused; re-upload it (with a fresh token) next time you change the DB.
 */

declare(strict_types=1);

// CHANGE THIS before uploading, e.g. TkPq92XmZ4wL. Use something only you know.
define('DEPLOY_TOKEN', 'CHANGE_THIS_BEFORE_UPLOAD');

if ($_GET['token'] ?? null !== DEPLOY_TOKEN) {
    http_response_code(403);
    exit('Invalid or missing token.');
}

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

/** @var array<int, array{0: string, 1: array<string, mixed>}> $commands */
$commands = [
    ['migrate', ['--force' => true]],
    ['optimize:clear', []],
];

header('Content-Type: text/plain; charset=utf-8');
echo "RIS deployment helper started.\n\n";

foreach ($commands as [$command, $arguments]) {
    echo "> php artisan {$command}\n";
    try {
        $status = $kernel->call($command, $arguments);
        echo "  exit code: {$status}\n\n";
    } catch (Throwable $e) {
        echo "  FAILED: {$e->getMessage()}\n\n";
        echo "Something went wrong. Keep this file, fix the issue, and retry.\n";

        exit(1);
    }
}

unlink(__FILE__);

echo "Done. This helper has removed itself.\n";