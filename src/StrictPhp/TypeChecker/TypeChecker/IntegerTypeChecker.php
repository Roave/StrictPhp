<?php

namespace StrictPhp\TypeChecker\TypeChecker;

use StrictPhp\TypeChecker\TypeCheckerInterface;

final class IntegerTypeChecker implements TypeCheckerInterface
{
    /**
     * @var string[]
     */
    private static $allowedTypes = ['int', 'integer'];

    /**
     * {@inheritDoc}
     */
    public function canApplyToType($type)
    {
        return in_array(strtolower($type), self::$allowedTypes, true);
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value)
    {
        return is_int($value);
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
