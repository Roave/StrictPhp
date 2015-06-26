# Basic Strict

With a configured bootstrap, we use notes on the properties to make your code work strictly.

The `StrictPhp` uses compatible with the PhpDocumentor annotation. Then you probably should be familiar with them.

Take a look at following class.

```php
class Sushi
{
    /**
     * @var int
     */
    public $price;
}
```

We're talking to `StrictPhp` we can only set a integer value for the `Sushi#price` property.

Let's try set a integer value.

```php
$sushi = new Sushi;
$sushi->price = 2;
```

That's work perfectly with the correct data.
Let's try pass a wrong data type for the property.

```php
$sushi = new Sushi;
$sushi->price = '2';
```

We have got a:

```
Fatal error: Uncaught exception 'ErrorException' ...
```

You're using the most basic common functionality of `StrictPhp`.

Great!
