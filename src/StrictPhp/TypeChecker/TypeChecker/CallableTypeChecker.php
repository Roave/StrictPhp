<?php

namespace StrictPhp\TypeChecker\TypeChecker;

use StrictPhp\TypeChecker\TypeCheckerInterface;

final class ArrayTypeChecker implements TypeCheckerInterface
{
    /**
     * @var callable|null
     */
    private static $failingCallback;

    /**
     * {@inheritDoc}
     */
    public function canApplyToType($type)
    {
        return strtolower($type) === 'callable';
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value, $type)
    {
        return is_int($value);
    }

    /**
     * {@inheritDoc}
     */
    public function simulateFailure($value, $type)
    {
        $callback = self::$failingCallback ?: self::$failingCallback = function (callable $value) {
            return $value;
        };

        $callback($value);
    }
}
