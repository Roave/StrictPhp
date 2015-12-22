<?php

namespace StrictPhp\Projector;

class MethodExecutor
{
    /**
     * @var mixed[]
     */
    public static $executionResults = [];

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
        return static::has($key)
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
        return array_key_exists($key, static::$executionResults);
    }
}
