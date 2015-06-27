# Immutability

We also provided a `@immutable` annotation, which works pretty much a constant, ideal to use on values objects, not mutable.

A property marked as immutable can receive a value only a single time. The value can never be overwritten.

A simple value object can be expressed using `StrictPhp` as the follow:

```php
class Sushi
{
    /**
     * @immutable
     */
    public $price;
}
```

If the value of `Sushi::$price` is gonna be changed, we will get an exception.

```php
$sushi = new Sushi;
$sushi->price = 2.0;

// Okay!

$sushi->price = 1.9; // Exception is raised

```
