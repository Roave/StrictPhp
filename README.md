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

## Features

`StrictPhp` currently supports following features:

#### Per-property type checks

This feature will prevent your application from assigning illegal values to
properties that are type-hinted (via docblock) differently. As an example,
consider following class:

```php
class Example
{
    /**
     * @var int|null
     */
    public $integer;
}
```

Following code will work:

```php
$object = new Example();

$object->integer = 123;
```

Following code will crash:

```php
$object = new Example();

$object->integer = '123';
```

Please note that this kind of feature currently only works with public and 
protected properties.

#### immutable properties

This feature will prevent your application from overwriting object properties
that are marked as `@immutable`. As an example, consider following class:

```php
class Example
{
    /**
     * @immutable
     */
    public $immutableProperty;
}
```

Following code will crash:

```php
$object = new Example();

$object->immutableProperty = 'a value';

echo 'Works till here!';

$object->immutableProperty = 'another value'; // crash
```

Please note that this kind of feature currently only works with public and 
protected properties.

## Current limitations

This package uses [voodoo magic](http://ocramius.github.io/voodoo-php/) to 
operate, specifically [go-aop-php](https://github.com/lisachenko/go-aop-php).

Go AOP PHP has some limitations when it comes to intercepting access to
private class members, so please be aware that it has limited scope (for now).

## License

This package is released under the [MIT license](LICENSE).

## Contributing

If you wish to contribute to the project, please read the [CONTRIBUTING notes](CONTRIBUTING.md).
