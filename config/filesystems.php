<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'uploader' => [
            'driver' => 'local',
            'root' => storage_path(env('STORAGE_UPLOADER_PATH')),
        ],

        'bags' => [
            'driver' => 'local',
            'root' => storage_path(env('STORAGE_BAGS_PATH')),
        ],
        'jobs' => [
            'driver' => 'local',
            'root' => storage_path(env('STORAGE_JOBS_PATH')),
        ],
        'messages-processing' => [
            'driver' => 'local',
            'root' => storage_path(env('STORAGE_MESSAGES_PROCESSING_PATH')),
        ],
        'messages-processed' => [
            'driver' => 'local',
            'root' => storage_path(env('STORAGE_MESSAGES_PROCESSED_PATH')),
        ],
        'messages-failed' => [
            'driver' => 'local',
            'root' => storage_path(env('STORAGE_MESSAGES_FAILED_PATH')),
        ],
        'archivematica' => [
            'driver' => 'local',
            'root' => storage_path(env('STORAGE_TRANSFER_PATH')),
        ],

        'local' => [
            'driver' => 'local',
            'root' => storage_path('/'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_URL'),
            'use_path_style_endpoint' => true
        ],

        'am' => [
           'driver' => 'local',
           'root' => (storage_path(env('STORAGE_TRANSFER_PATH'))),
        ],
        'am_aip' => [
            'driver' => 'local',
            'root' => (storage_path(env('STORAGE_AIP_PATH'))),
        ],
        'am_dip' => [
            'driver' => 'local',
            'root' => (storage_path(env('STORAGE_DIP_PATH'))),
        ],
        'outgoing' => [
            'driver' => 'local',
            'root' => ( storage_path( env( 'STORAGE_OUTGOING_DOWNLOADS_FOLDER', 'outgoing' ) ) )
        ],
        'testdata' => [
            'driver' => 'local',
            'root' => ( storage_path( env( 'STORAGE_TESTDATA', 'testdata' ) ) )
        ],
    ],

];
