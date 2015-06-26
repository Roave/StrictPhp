<?php

namespace StrictPhp\TypeChecker\TypeChecker;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Object_;
use StrictPhp\TypeChecker\TypeCheckerInterface;

final class GenericObjectTypeChecker implements TypeCheckerInterface
{
    /**
     * {@inheritDoc}
     */
    public function canApplyToType(Type $type)
    {
        return $type instanceof Object_;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value, Type $type)
    {
        return is_object($value);
    }

    /**
     * {@inheritDoc}
     */
    public function simulateFailure($value, Type $type)
    {
        if (! $this->validate($value, $type)) {
            // @TODO bump to PHP 7 and use strict scalar types + a closure.
            throw new \ErrorException('NOPE');
        }
    }
}
