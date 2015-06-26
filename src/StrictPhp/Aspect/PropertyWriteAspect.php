<?php

namespace StrictPhp\Aspect;

use Go\Aop\Aspect;
use Go\Aop\Intercept\FieldAccess;
use Go\Lang\Annotation as Go;
use phpDocumentor\Reflection\DocBlock;

final class PropertyWriteAspect implements Aspect
{
    /**
     * @var callable[]
     */
    private $propertyWriteCheckers;

    /**
     * @param callable ...$propertyWriteCheckers
     */
    public function __construct(callable ...$propertyWriteCheckers)
    {
        $this->propertyWriteCheckers = $propertyWriteCheckers;
    }

    /**
     * @Go\Before("access(public|protected **->*)")
     *
     * @param FieldAccess $access
     *
     * @return mixed
     */
    public function beforePropertyAccess(FieldAccess $access)
    {
        if (FieldAccess::WRITE !== $access->getAccessType()) {
            return $access->proceed();
        }

        foreach ($this->propertyWriteCheckers as $checker) {
            $checker($access);
        }

        return $access->proceed();
    }
}
