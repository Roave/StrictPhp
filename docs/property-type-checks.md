---
currentMenu: property-type-checks
---

# Per-property type checks

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

**Next step:** [Return type checks](return-type-checks.md)
