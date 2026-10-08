<?php

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

        // Upload URLs are kept relative to the current host: an absolute
        // APP_URL bakes one domain into every generated media link, which
        // breaks as soon as the app runs under another domain (stale .env on
        // a live server, a local environment, a preview deployment).
        'public' => [
            'driver' => 'local',
            'root' => public_path('images'),
            'url' => '/images',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        'public_files' => [
            'driver' => 'local',
            'root' => public_path('files'),
            'url' => '/files',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Uploads are written straight into the "public" tree (public/images and
    | public/files), so no symbolic link is required to serve them. This is
    | declared as an explicit empty array to stop the framework default of
    | public/storage -> storage/app/public from being merged back in.
    |
    */

    'links' => [],

];
