<?php

namespace StrictPhp\Aspect;

use Go\Aop\Aspect;
use Go\Aop\Intercept\FieldAccess;
use Go\Lang\Annotation as Go;
use phpDocumentor\Reflection\DocBlock;
use StrictPhp\TypeChecker\ApplyTypeChecks;
use StrictPhp\TypeChecker\TypeChecker\ArrayTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\CallableTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\GenericObjectTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\StringTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker;
use StrictPhp\TypeFinder\PropertyTypeFinder;

class ImmutablePropertyCheck implements Aspect
{
    /**
     * @Go\Before("access(public **->*)")
     *
     * @param FieldAccess $access
     *
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public function beforePropertyAccess(FieldAccess $access)
    {
        if (! $that = $access->getThis()) {
            return $access->proceed();
        }

        if (FieldAccess::WRITE !== $access->getAccessType()) {
            return $access->proceed();
        }

        $field = $access->getField();

        $field->setAccessible(true);

        // simplistic check - won't check for multiple assignments of "null" to a "null" valued field
        if (null === ($currentValue = $field->getValue($that))) {
            return $access->proceed();
        }

        if (! (new DocBlock($field))->getTagsByName('immutable')) {
            return $access->proceed();
        }

        $newValue = $access->getValueToSet();

        throw new \RuntimeException(sprintf(
            'Trying to overwrite property %s#$%s of object %s#%s with a value of type "%s".'
            . ' The property was already given a value of type %s',
            $field->getDeclaringClass()->getName(),
            $field->getName(),
            is_object($newValue) ? get_class($newValue) : gettype($newValue),
            get_class($that),
            spl_object_hash($that),
            is_object($currentValue) ? get_class($currentValue) : gettype($currentValue)
        ));
    }
}
