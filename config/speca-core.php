<?php

// Config for speca/speca-core.
return [
    'name' => 'paydunya-core',

    'route' => [
        'api' => [
            'prefix' => 'api/v1',
            'domain' => '',
        ],
        'web' => [
            'prefix' => '',
            'domain' => '',
        ],
    ],

    'scramble' => [
        'doc-path' => env('PAYDUNYA_CORE_SCRAMBLE_DOC_PATH', 'docs/api'),
        'json-path' => env('PAYDUNYA_CORE_SCRAMBLE_JSON_PATH', 'docs/api.json'),
    ],

    'who-can-access-developers-tools' => [],
];
