# Enabling StrictPhp

To bootstrap `StrictPhp`, we initialize the `StrictPhp\StrictPhpKernel` singleton:

```php
\StrictPhp\StrictPhpKernel::getInstance()->init([
    'debug'        => true,
    'cacheDir'     => sys_get_temp_dir(),
    'includePaths' => [
        __DIR__ . '/path/to/your/sources',
    ],
]);
```

More supported configuration keys can be found in the [Go! AOP PHP documentation](http://go.aopphp.com/docs/initial-configuration)

**Next step:** [Basic strict property typing](basic-strict.md)
