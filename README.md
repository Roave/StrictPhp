# StrictPhp

[![Build Status](https://travis-ci.org/Roave/StrictPhp.svg)](https://travis-ci.org/Roave/StrictPhp)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Roave/StrictPhp/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Roave/StrictPhp/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Roave/StrictPhp/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Roave/StrictPhp/?branch=master)

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
    'cacheDir'     => sys_get_temp_dir(),
    'includePaths' => [
        __DIR__ . '/path/to/your/sources',
    ],
]);
```

StrictPhp will then intercept any runtime operations that are considered "illegal"
and throw an exception or a catchable fatal error.

Please remember to execute this code **before** any code that may autoload any of
the classes that should be checked.

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

#### Public constructor property initialization checks

This feature of StrictPhp allows checking whether a public constructor of
a class fully initialized an object.

Following code will make StrictPhp crash your application:

```php
class Example
{
    /**
     * @var array
     */
    private $arrayProperty;
    
    public function __construct()
    {
    }
}
```

In order to make this code work, you have to either annotate `$arrayProperty`
with `@var array|null`, or make the constructor initialize the property
correctly:

```php
class Example
{
    /**
     * @var array
     */
    private $arrayProperty;
    
    public function __construct()
    {
        $this->arrayProperty = ['initial status'];
    }
}
```

#### Parameter interface jailing

This feature of StrictPhp "jails" (restricts) calls to non-interfaced methods
whenever an interface is used as a type-hint.

Following example will work, but will crash if StrictPhp is enabled:

```php
interface HornInterface
{
    public function honk();
}

class TheUsualHorn implements HornInterface
{
    public function honk() { var_dump('honk'); }
    public function sadTrombone() { var_dump('pooapooapooapoaaaa'); }
}

class Car
{
    public function honk(HornInterface $horn, $sad = false)
    {
        if ($sad) {
            // method not covered by interface: crash
            $horn->sadTrombone();
            
            return;
        }
        
        // interface respected
        $horn->honk();
    }
}
```

```php
$car  = new Car();
$horn = new TheUsualHorn();

$car->honk($horn, false); // works
$car->honk($horn, true); // crashes
```

This prevents consumers of your APIs to design their code against non-API methods.

## Current limitations

This package uses [voodoo magic](http://ocramius.github.io/voodoo-php/) to 
operate, specifically [go-aop-php](https://github.com/lisachenko/go-aop-php).

Go AOP PHP has some limitations when it comes to intercepting access to
private class members, so please be aware that it has limited scope (for now).

This package only works against autoloaded classes: classes that aren't handled by
an autoloader cannot be rectified by StrictPhp.

## License

This package is released under the [MIT license](LICENSE).

## Contributing

If you wish to contribute to the project, please read the [CONTRIBUTING notes](CONTRIBUTING.md).
