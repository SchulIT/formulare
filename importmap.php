<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'backend' => [
        'path' => './assets/js/backend.js',
        'entrypoint' => true,
    ],
    'frontend' => [
        'path' => './assets/js/frontend.js',
        'entrypoint' => true,
    ],
    'collection' => [
        'path' => './assets/js/collection.js',
        'entrypoint' => true,
    ],
    'bootstrap' => [
        'version' => '5.3.8',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.3.8',
        'type' => 'css',
    ],
    '@fortawesome/fontawesome-free/css/all.css' => [
        'version' => '7.2.0',
        'type' => 'css',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
];
