<?php

namespace StrictPhp\TypeChecker\TypeChecker;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Mixed;
use phpDocumentor\Reflection\Types\Null_;
use StrictPhp\TypeChecker\TypeCheckerInterface;

final class NullTypeChecker implements TypeCheckerInterface
{
    /**
     * {@inheritDoc}
     */
    public function canApplyToType(Type $type)
    {
        return $type instanceof Null_;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function validate($value, Type $type)
    {
        if (! $type instanceof Null_) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid type "%s" given, expecting "%s"',
                get_class($type),
                Mixed::class
            ));
        }

        return null === $value;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function simulateFailure($value, Type $type)
    {
        if (! $this->validate($value, $type)) {
            // @TODO bump to PHP 7 and use strict scalar types + a closure.
            throw new \ErrorException('NOPE');
        }
    }
}
