# StrictPhp

StrictPhp is a development tool aimed at bringing stricter runtime assertions
into PHP applications and libraries.

## Installation

```sh
composer require roave/strict-php
```

Please note that the current version has unstable dependencies. We will get rid
of those as we approach `1.0.0`.

## Usage

After installing `StrictPhp`, point it at the directory to be checked at runtime
(the code that you are writing) via following code:

```php
\StrictPhp\StrictPhpKernel::getInstance()->init([
    'debug'        => true,
    // change this if you use this tool on multiple projects:
    'cacheDir'     => sys_get_tmp_dir(),
    'includePaths' => [
        __DIR__ . '/path/to/your/sources',
    ],
]);
```

StrictPhp will then intercept any runtime operations that are considered "illegal"
and throw an exception or a catchable fatal error.