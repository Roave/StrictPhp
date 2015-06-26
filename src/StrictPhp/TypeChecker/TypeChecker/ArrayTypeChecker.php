<?php

namespace StrictPhp\TypeChecker\TypeChecker;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
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
    public function canApplyToType(Type $type)
    {
        return $type instanceof Array_;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value, Type $type)
    {
        return is_array($value);
    }

    /**
     * {@inheritDoc}
     */
    public function simulateFailure($value, Type $type)
    {
        $callback = self::$failingCallback ?: self::$failingCallback = function (array $value) {
            return $value;
        };

        $callback($value);
    }
}
