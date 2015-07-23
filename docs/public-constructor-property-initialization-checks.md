---
currentMenu: public-constructor-property-initialization-checks
---

# Public constructor property initialization checks

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

**Next step:** [Parameter interface jailing](parameter-interface-jailing.md)
