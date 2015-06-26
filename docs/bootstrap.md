# Being Strict

Start being strict is very easy, at first we just need to configure an instance of `\StrictPhp\StrictPhpKernel`
configured properly.

```php
\StrictPhp\StrictPhpKernel::getInstance()->init([
    'debug'        => true,
    'cacheDir'     => sys_get_temp_dir(),
    'includePaths' => [
        __DIR__ . '/path/to/your/sources',
    ],
]);
```

More details about the options settings that can be passed to the `init` can be seen in the documentation of the
[Go! AOP Framework](https://github.com/lisachenko/go-aop-php)

It's all you need to start being Strict!
