---
currentMenu: return-type-checks
---

# Return type checks

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

**Next step:** [Immutability](immutability.md)
