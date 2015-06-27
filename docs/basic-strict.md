# Basic strict property typing

With a configured bootstrap, we use annotations on the properties to make your app operate in strict mode.

The `StrictPhp` uses compatible with the PhpDocumentor annotation. Then you probably should be familiar with them.

Take a look at following class.

```php
class Sushi
{
    /**
     * @var float
     */
    public $price;
}
```

In that way, `StrictPhp` knows we can only set a **float* value for the `Sushi#price` property.

Let's try set a integer value.

```php
$sushi = new Sushi;
$sushi->price = 2.0;
```

That works perfectly fine.

Let's try assigning a wrong data type to the property.

```php
$sushi = new Sushi;
$sushi->price = '2';
```

We will get an exception:

```
Fatal error: Uncaught exception 'ErrorException' ...
```

You're using the most basic common functionality of `StrictPhp`.

Great!
