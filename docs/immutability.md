# Immutability

We also provided a `@immutable` annotation, who works pretty much a constant, ideal to use on values objects, not mutable.

A property setted as immutable can receive a value only a single time. Your value never can be changed.

The following code...

```php
class Sushi
{
    private $price;

    public function __construct($price)
    {
        $this->price = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }
}
```

Can be expressed using `StrictPhp` as th follow:

```php
class Sushi
{
    /**
     * @immutable
     */
    public $price;
}
```
