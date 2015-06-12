<?php

namespace StrictPhp\TypeChecker\TypeChecker;

use StrictPhp\TypeChecker\TypeCheckerInterface;

final class GenericObjectTypeChecker implements TypeCheckerInterface
{
    /**
     * {@inheritDoc}
     */
    public function canApplyToType($type)
    {
        return strtolower($type) === 'object' || class_exists($type, false);
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value, $type)
    {
        return is_object($value);
    }

    /**
     * {@inheritDoc}
     */
    public function simulateFailure($value, $type)
    {
        if (! $this->validate($value, $type)) {
            // @TODO bump to PHP 7 and use strict scalar types + a closure.
            throw new \ErrorException('NOPE');
        }
    }
}
