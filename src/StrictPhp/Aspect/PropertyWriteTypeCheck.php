<?php

namespace StrictPhp\Aspect;

use Go\Aop\Aspect;
use Go\Aop\Intercept\FieldAccess;
use Go\Lang\Annotation as Go;
use StrictPhp\TypeChecker\TypeChecker;
use StrictPhp\TypeFinder\PropertyTypeFinder;

class PropertyWriteTypeCheck implements Aspect
{
    /**
     * @Go\Before("access(public **->*)")
     */
    public function beforePropertyAccess(FieldAccess $access)
    {
        (new TypeChecker())->__invoke(
            (new PropertyTypeFinder())->__invoke($access->getField()),
            $access->getValueToSet()
        );

        return $access->proceed();
    }
}
