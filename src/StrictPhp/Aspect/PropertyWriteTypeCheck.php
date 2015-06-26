<?php

namespace StrictPhp\Aspect;

use Go\Aop\Aspect;
use Go\Aop\Intercept\FieldAccess;
use Go\Lang\Annotation as Go;
use StrictPhp\TypeChecker\ApplyTypeChecks;
use StrictPhp\TypeChecker\TypeChecker\ArrayTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\CallableTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\GenericObjectTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\StringTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker;
use StrictPhp\TypeFinder\PropertyTypeFinder;

class PropertyWriteTypeCheck implements Aspect
{
    /**
     * @Go\Before("access(public **->*)")
     *
     * @param FieldAccess $access
     *
     * @return mixed
     *
     * @throws \ErrorException|\Exception
     */
    public function beforePropertyAccess(FieldAccess $access)
    {
        if (FieldAccess::WRITE !== $access->getAccessType()) {
            return $access->proceed();
        }

        $baseCheckers = [
            new IntegerTypeChecker(),
            new ArrayTypeChecker(),
            new CallableTypeChecker(),
            new StringTypeChecker(),
            new GenericObjectTypeChecker(),
            new ObjectTypeChecker(),
        ];

        $object       = $access->getThis();
        $contextClass = $object ? get_class($object) : $access->getField()->getDeclaringClass()->getName();

        (new ApplyTypeChecks(
            new TypedTraversableChecker(...$baseCheckers),
            ...$baseCheckers
        ))->__invoke(
            (new PropertyTypeFinder())->__invoke($access->getField(), $contextClass),
            $access->getValueToSet()
        );

        return $access->proceed();
    }
}
