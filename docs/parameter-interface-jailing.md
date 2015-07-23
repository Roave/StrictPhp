---
currentMenu: parameter-interface-jailing
---

# Parameter interface jailing

Enabled via flag `StrictPhp\StrictPhpKernel::JAIL_PUBLIC_METHOD_PARAMETERS`.

This feature of StrictPhp "jails" (restricts) calls to non-interfaced methods
whenever an interface is used as a type-hint.

Following example will work, but will crash if StrictPhp is enabled:

```php
interface HornInterface
{
    public function honk();
}

class TheUsualHorn implements HornInterface
{
    public function honk() { var_dump('honk'); }
    public function sadTrombone() { var_dump('pooapooapooapoaaaa'); }
}

class Car
{
    public function honk(HornInterface $horn, $sad = false)
    {
        if ($sad) {
            // method not covered by interface: crash
            $horn->sadTrombone();

            return;
        }

        // interface respected
        $horn->honk();
    }
}
```

```php
$car  = new Car();
$horn = new TheUsualHorn();

$car->honk($horn, false); // works
$car->honk($horn, true); // crashes
```

This prevents consumers of your APIs to design their code against non-API methods.

**Next step:** [Parameter checking](parameter-checking.md)
