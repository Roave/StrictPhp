<?php

namespace StrictPhp\TypeChecker\TypeChecker;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Object_;
use StrictPhp\TypeChecker\TypeCheckerInterface;

final class ObjectTypeChecker implements TypeCheckerInterface
{
    /**
     * {@inheritDoc}
     */
    public function canApplyToType(Type $type)
    {
        return $type instanceof Object_
            && ($fqsen = $type->getFqsen())
            && class_exists($fqsen->getName());
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function validate($value, Type $type)
    {
        if (! $type instanceof Object_) {
            throw new \InvalidArgumentException(sprintf(
                'Non-object type "%s" given, expected "%s"',
                get_class($type),
                Object_::class
            ));
        }

        if (! $fqcn = $type->getFqsen()) {
            throw new \InvalidArgumentException(sprintf(
                'The provided type of type "%s" does not have a FQCN',
                get_class($type)
            ));
        }

        $fqcnString = $fqcn->getName();

        return $value instanceof $fqcnString;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function simulateFailure($value, Type $type)
    {
        if (! $this->canApplyToType($type)) {
            throw new \InvalidArgumentException(sprintf(
                'The provided type "%s" does not refer to a valid class',
                $type
            ));
        }

        /* @var $callback callable */
        $callback = eval(sprintf('return function (%s $value) {};', $type));

        $callback($value);
    }
}
