# Basic strict property typing

With a configured bootstrap, we use annotations on the properties to make your app operate in strict mode.

`StrictPhp` uses PhpDocumentor compatible annotations.

Take a look at the following class.

```php
class Sushi
{
    /**
     * @var float
     */
    public $price;
}
```

In this way, `StrictPhp` knows that we can only set a **float** value for the `Sushi#price` property.

Let's try setting a float value.

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

`StrictPhp` provide a way to work strictly with a collection of a data type.

We can have a collection of a type marking the property with something like `string[]`, also we can have more levels `string[][]` and soon.

```php
/**
 * @var Invoice[]
 */
public $invoice;
```

This can receive *only* a collection of `Invoice` objects. If any element on the array assigned to `$invoice` is not an
instance of `Invoice` we will get an `Exception`.

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
- *Class|Interfaces names*
- `[]` - *collection*

**Next step:** [Immutability](immutability.md)
