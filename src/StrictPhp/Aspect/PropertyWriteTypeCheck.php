<?php

namespace StrictPhp\Aspect;

use Go\Aop\Aspect;
use Go\Aop\Intercept\FieldAccess;
use Go\Lang\Annotation as Go;

class PropertyWriteTypeCheck implements Aspect
{
    /**
     * @Go\Before("access(public **->*)")
     */
    public function beforePropertyAccess(FieldAccess $access)
    {
        var_dump(get_class($access->getThis()) . '#' . $access->getField()->getName());

        return $access->proceed();
    }
}
