# StrictPhp

[![Build Status](https://travis-ci.org/Roave/StrictPhp.svg)](https://travis-ci.org/Roave/StrictPhp)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Roave/StrictPhp/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Roave/StrictPhp/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/Roave/StrictPhp/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Roave/StrictPhp/?branch=master)

StrictPhp is a development tool aimed at bringing stricter runtime assertions
into PHP applications and libraries.

## Authors

- [Marco Pivetta](https://github.com/Ocramius)
- [Jefersson Nathan](https://github.com/malukenho)

## Installation

```sh
$ composer require roave/strict-php
```

Please note that the current version has unstable dependencies.

In order to install those dependencies, you can set `"minimum-stability"` in
your `composer.json`:

```json
{
    "minimum-stability": "dev"
}
```

## Usage

After installing `StrictPhp`, point it at the directory to be checked at runtime
(the code that you are writing) via following code:

```php
\StrictPhp\StrictPhpKernel::bootstrap([
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

## Configuration

The `StrictPhp\StrictPhpKernel` can be initialized with a set of [options to be passed
to go-aop-php](http://go.aopphp.com/docs/initial-configuration/) and a set of feature
flags:

 - `StrictPhp\StrictPhpKernel::CHECK_STATE_AFTER_CONSTRUCTOR_CALL`
 - `StrictPhp\StrictPhpKernel::JAIL_PUBLIC_METHOD_PARAMETERS`
 - `StrictPhp\StrictPhpKernel::CHECK_STATE_AFTER_PUBLIC_METHOD_CALL`
 - `StrictPhp\StrictPhpKernel::CHECK_PUBLIC_METHOD_PARAMETER_TYPE`
 - `StrictPhp\StrictPhpKernel::CHECK_PUBLIC_METHOD_RETURN_TYPE`
 - `StrictPhp\StrictPhpKernel::CHECK_PROPERTY_WRITE_IMMUTABILITY`
 - `StrictPhp\StrictPhpKernel::CHECK_PROPERTY_WRITE_TYPE`
 
Each of these features are described below.

## Features

`StrictPhp` currently supports following features:

#### Per-property type checks

Enabled via flag `StrictPhp\StrictPhpKernel::CHECK_PROPERTY_WRITE_TYPE`.

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

#### Return type checks

Quite similar to the above functionality, this feature will prevent your application 
from **returning** illegal values from methods that are type-hinted (via docblock) 
differently. As an example, consider following method:

```php
class Example
{
    /**
     * @return string
     */
    public function dummyReturn($value)
    {
        return $value;
    }
}
```

Following code will work:

```php
(new Example())->dummyReturn('string');
```

Following code will crash:

```php
(new Example())->dummyReturn(123);
```

Please note that this kind of feature currently only works with public and 
protected methods.

#### immutable properties

Enabled via flag `StrictPhp\StrictPhpKernel::CHECK_PROPERTY_WRITE_IMMUTABILITY`.

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

Enabled via flag `StrictPhp\StrictPhpKernel::CHECK_STATE_AFTER_CONSTRUCTOR_CALL`.

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

Enabled via flag `StrictPhp\StrictPhpKernel::JAIL_PUBLIC_METHOD_PARAMETERS`.

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

#### Parameter checking

Enabled via flag `StrictPhp\StrictPhpKernel::CHECK_PUBLIC_METHOD_PARAMETER_TYPE`.

StrictPhp also provides a way to check parameters types in more detail during
public method calls.

Specifically, the following code will work in PHP:

```php
final class Invoice
{
    /**
     * @param LineItem[] $lineItems
     */
    public function __construct(array $lineItems)
    {
        // ...
    }
}

$invoice = new Invoice(['foo', 'bar']);
```

This code will crash in StrictPhp due to the type mismatch in `$lineItems` (which
should be a collection of `LineItem` objects instead).

## Current limitations

This package uses [voodoo magic](http://ocramius.github.io/voodoo-php/) to 
operate, specifically [go-aop-php](https://github.com/lisachenko/go-aop-php).

Go AOP PHP has some limitations when it comes to intercepting access to
private class members, so please be aware that it has limited scope (for now).

This package only works against autoloaded classes; classes that aren't handled by
an autoloader cannot be rectified by StrictPhp.

## License

This package is released under the [MIT license](LICENSE).

## Contributing

If you wish to contribute to the project, please read the [CONTRIBUTING notes](CONTRIBUTING.md).
