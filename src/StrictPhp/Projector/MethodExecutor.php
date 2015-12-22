<?php

namespace StrictPhp\Projector;

class MethodExecutor
{
    /**
     * @var mixed[]
     */
    private static $executionResults = [];

    /**
     * @param string $key
     * @param mixed  $value
     */
    public static function store($key, $value)
    {
        static::$executionResults[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public static function retrieve($key)
    {
        return isset(static::$executionResults[$key])
            ? static::$executionResults[$key]
            : null;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public static function has($key)
    {
        return isset(static::$executionResults[$key]) && static::$executionResults[$key];
    }
}
