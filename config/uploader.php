<?php

return [

    'uploader_folder' => storage_path() . '/incoming',

    'temp_folder' => '/temp',

    'fine_uploader' => [

        'allowed_extensions' => [],
        'size_limit'    => 1024*1024*10, // 10MB
        'input_name'    => 'qqfile',
        'chunks_folder' => '/chunks'

    ],

    // Can be overridden by client
    'thumbnails' => [
        'height' => 100,
        'width' => 100,
        'crop' => 'fill'
    ],

    'storage' => 'local',

    'storage_url_resolver' => function($file) {
        return '/' . $file->getUploaderPath() . '/' . $file->getFilename();
    },

    'success_response_class' => Optimus\FineuploaderServer\Response\OptimusResponse::class,

    'storages' => [

        'local' => [
            'class' => Optimus\FineuploaderServer\Storage\LocalStorage::class,
            'config' => [
                'root_folder' => storage_path() . '/uploader'
            ]
        ]

    ],

    'naming_strategy' => Optimus\FineuploaderServer\Naming\UniqidStrategy::class,
    'middleware' => [
        [
            'class' => App\BypassMiddleware::class,
            'config' => [
            ]
        ]
    ]

];
