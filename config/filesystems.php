<?php

$cellarHost = env('CELLAR_ADDON_HOST');
$cellarEndpoint = $cellarHost
    ? (str_contains($cellarHost, '://') ? $cellarHost : 'https://'.$cellarHost)
    : env('AWS_ENDPOINT');
$cellarBucket = env('CELLAR_ADDON_BUCKET') ?: env('AWS_BUCKET');
$cellarUrl = env('AWS_URL') ?: ($cellarEndpoint && $cellarBucket ? rtrim($cellarEndpoint, '/').'/'.trim($cellarBucket, '/') : null);

$s3Config = [
    'driver' => 's3',
    'key' => env('CELLAR_ADDON_KEY_ID') ?: env('AWS_ACCESS_KEY_ID'),
    'secret' => env('CELLAR_ADDON_KEY_SECRET') ?: env('AWS_SECRET_ACCESS_KEY'),
    'region' => env('CELLAR_REGION') ?: env('AWS_DEFAULT_REGION', 'us-east-1'),
    'bucket' => $cellarBucket,
    'url' => $cellarUrl,
    'endpoint' => $cellarEndpoint,
    'use_path_style_endpoint' => $cellarHost ? true : (bool) env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    'visibility' => 'public',
    'throw' => false,
    'report' => false,
];

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => $cellarHost ? $s3Config : [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => $s3Config,

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
