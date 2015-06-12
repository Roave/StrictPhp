<?php

namespace StrictPhp\Aspect;

use Go\Aop\Aspect;
use Go\Aop\Intercept\FieldAccess;
use Go\Lang\Annotation as Go;
use StrictPhp\TypeChecker\ApplyTypeChecks;
use StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker;
use StrictPhp\TypeFinder\PropertyTypeFinder;

class PropertyWriteTypeCheck implements Aspect
{
    /**
     * @Go\Before("access(public **->*)")
     *
     * @throws \ErrorException|\Exception
     */
    public function beforePropertyAccess(FieldAccess $access)
    {
        (new ApplyTypeChecks(...[
            new IntegerTypeChecker(),
        ]))->__invoke(
            (new PropertyTypeFinder())->__invoke($access->getField()),
            $access->getValueToSet()
        );

        return $access->proceed();
    }
}
