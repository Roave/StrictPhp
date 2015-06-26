<?php

namespace StrictPhp\TypeChecker\TypeChecker;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Callable_;
use StrictPhp\TypeChecker\TypeCheckerInterface;

final class CallableTypeChecker implements TypeCheckerInterface
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
        return $type instanceof Callable_;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value, Type $type)
    {
        return is_callable($value);
    }

    /**
     * {@inheritDoc}
     */
    public function simulateFailure($value, Type $type)
    {
        $callback = self::$failingCallback ?: self::$failingCallback = function (callable $value) {
            return $value;
        };

        $callback($value);
    }
}
