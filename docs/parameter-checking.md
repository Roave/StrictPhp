---
currentMenu: parameter-checking
---

# Parameter checking

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

**Next step:** [Current limitations](current-limitation.md)
