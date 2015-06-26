<?php

namespace StrictPhp\AccessChecker;

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

final class PropertyWriteTypeChecker
{
    /**
     * @param FieldAccess $access
     *
     * @return void
     *
     * @throws \ErrorException|\Exception
     */
    public function __invoke(FieldAccess $access)
    {
        if (FieldAccess::WRITE !== $access->getAccessType()) {
            return;
        }

        $that         = $access->getThis();
        $contextClass = $that ? get_class($that) : $access->getField()->getDeclaringClass()->getName();

        $baseCheckers = [
            new IntegerTypeChecker(),
            new ArrayTypeChecker(),
            new CallableTypeChecker(),
            new StringTypeChecker(),
            new GenericObjectTypeChecker(),
            new ObjectTypeChecker(),
        ];

        (new ApplyTypeChecks(
            new TypedTraversableChecker(...$baseCheckers),
            ...$baseCheckers
        ))->__invoke(
            (new PropertyTypeFinder())->__invoke($access->getField(), $contextClass),
            $access->getValueToSet()
        );
    }
}
