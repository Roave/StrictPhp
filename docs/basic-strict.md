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

In that way, `StrictPhp` knows we can only set a **float** value for the `Sushi#price` property.

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

This is the most basic functionality of `StrictPhp`

## Working with collections

`StrictPhp` provide a way to work strictly with a collection of elements.

We can have a collection declaring a type and the array symbol after type, as `string[]`, we can have multi array levels
expressed as `string[][]` and soon.

```php
/**
 * @var Invoice[]
 */
public $invoice;
```

This declare a collection of `Invoice`. If any element on the array assigned to `$invoice` is not a instance of `Invoice`,
it's will raise an `Exception`.

## Supported annotation types

- null
- int|integer
- mixed
- float
- string
- array
- callable
- object
- self
- static
- self
- *Class|Interfaces names*
- `[]` - *collection*

**Next step:** [Immutability](immutability.md)
