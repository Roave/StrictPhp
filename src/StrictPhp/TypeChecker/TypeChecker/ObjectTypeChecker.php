<?php

namespace StrictPhp\TypeChecker\TypeChecker;

use StrictPhp\TypeChecker\TypeCheckerInterface;

final class ObjectTypeChecker implements TypeCheckerInterface
{
    /**
     * {@inheritDoc}
     */
    public function canApplyToType($type)
    {
        return class_exists($type);
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value, $type)
    {
        return $value instanceof $type;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function simulateFailure($value, $type)
    {
        if (! $this->canApplyToType($type)) {
            throw new \InvalidArgumentException(sprintf('The provided type "%s" is not a valid class', $type));
        }

        /* @var $callback callable */
        $callback = eval(sprintf('return function (%s $value) {};', $type));

        $callback($value);
    }
}
