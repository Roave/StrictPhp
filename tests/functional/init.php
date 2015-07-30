<?php

use StrictPhp\StrictPhpKernel;

require __DIR__ . '/../../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

StrictPhpKernel::bootstrap(
    [
        'debug'        => true,
        'cacheDir'     => realpath(__DIR__ . '/..') . '/integration-tests-go-cache/',
        'includePaths' => [
            realpath(__DIR__ . '/../StrictPhpTestAsset'),
        ],
    ],
    [
        StrictPhpKernel::CHECK_STATE_AFTER_CONSTRUCTOR_CALL,
        StrictPhpKernel::JAIL_PUBLIC_METHOD_PARAMETERS,
        StrictPhpKernel::CHECK_STATE_AFTER_PUBLIC_METHOD_CALL,
        StrictPhpKernel::CHECK_PUBLIC_METHOD_PARAMETER_TYPE,
        StrictPhpKernel::CHECK_PUBLIC_METHOD_RETURN_TYPE,
        StrictPhpKernel::CHECK_PROPERTY_WRITE_IMMUTABILITY,
        StrictPhpKernel::CHECK_PROPERTY_WRITE_TYPE,
    ]
);
