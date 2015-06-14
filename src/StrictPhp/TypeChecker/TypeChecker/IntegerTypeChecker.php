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
    public function validate($value, $type)
    {
        return is_int($value);
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
