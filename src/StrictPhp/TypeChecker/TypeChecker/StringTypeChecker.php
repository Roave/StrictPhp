<?php

namespace StrictPhp\TypeChecker\TypeChecker;

use StrictPhp\TypeChecker\TypeCheckerInterface;

final class StringTypeChecker implements TypeCheckerInterface
{
    /**
     * {@inheritDoc}
     */
    public function canApplyToType($type)
    {
        return strtolower($type) === 'string';
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value)
    {
        return is_string($value);
    }

    /**
     * {@inheritDoc}
     */
    public function simulateFailure($value)
    {
        if (! $this->validate($value)) {
            // @TODO bump to PHP 7 and use strict scalar types + a closure.
            throw new \ErrorException('NOPE');
        }
    }
}
