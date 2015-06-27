# Enabling StrictPhp

To bootstrap `StrictPhp`, we initialize the `StrictPhp\StrictPhpKernel` singleton:

```php
\StrictPhp\StrictPhpKernel::getInstance()->init([
    'debug'        => true, // Use 'false' for production mode
    'cacheDir'     => sys_get_temp_dir(), // Adjust this path if needed
    'includePaths' => [
        __DIR__ . '/path/to/your/sources', // Include paths restricts the directories
                                           // where aspects should be applied, or empty
                                           // for all source files
    ],
]);
```

More supported configuration keys can be found in the [Go! AOP PHP documentation](http://go.aopphp.com/docs/initial-configuration)

**Next step:** [Basic strict property typing](basic-strict.md)
