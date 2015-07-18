<?php

require __DIR__ . '/../../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

\StrictPhp\StrictPhpKernel::bootstrap([
    'debug'        => true,
    'cacheDir'     => realpath(__DIR__ . '/..') . '/integration-tests-go-cache/',
    'includePaths' => [
        realpath(__DIR__ . '/../StrictPhpTestAsset'),
    ],
]);
