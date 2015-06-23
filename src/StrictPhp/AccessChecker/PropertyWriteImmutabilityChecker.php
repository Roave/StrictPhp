<?php

namespace StrictPhp\AccessChecker;

use Go\Aop\Intercept\FieldAccess;
use Go\Lang\Annotation as Go;
use phpDocumentor\Reflection\DocBlock;

final class PropertyWriteImmutabilityChecker
{
    /**
     * @param FieldAccess $access
     *
     * @return void
     *
     * @throws \RuntimeException
     */
    public function __invoke(FieldAccess $access)
    {
        if (! $that = $access->getThis()) {
            return;
        }

        if (FieldAccess::WRITE !== $access->getAccessType()) {
            return;
        }

        $field = $access->getField();

        $field->setAccessible(true);

        // simplistic check - won't check for multiple assignments of "null" to a "null" valued field
        if (null === ($currentValue = $field->getValue($that))) {
            return;
        }

        if (! (new DocBlock($field))->getTagsByName('immutable')) {
            return;
        }

        $newValue = $access->getValueToSet();

        throw new \RuntimeException(sprintf(
            'Trying to overwrite property %s#$%s of object %s#%s with a value of type "%s".'
            . ' The property was already given a value of type %s',
            $field->getDeclaringClass()->getName(),
            $field->getName(),
            get_class($that),
            spl_object_hash($that),
            is_object($newValue) ? get_class($newValue) : gettype($newValue),
            is_object($currentValue) ? get_class($currentValue) : gettype($currentValue)
        ));
    }
}
