<?php

namespace StrictPhp\Aspect;

use Go\Aop\Aspect;
use Go\Aop\Intercept\ConstructorInvocation;
use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation as Go;
use ReflectionProperty;
use StrictPhp\TypeChecker\ApplyTypeChecks;
use StrictPhp\TypeChecker\TypeChecker\ArrayTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\CallableTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\GenericObjectTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\StringTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker;
use StrictPhp\TypeFinder\PropertyTypeFinder;

class PostConstructStateCheck implements Aspect
{
    //* @Go\After("initialization(**)")
    /**
     * @Go\After("execution(public|protected|private **->__construct(*))")
     *
     * @param MethodInvocation $constructorInvocation
     *
     * @return mixed
     *
     * @throws \ErrorException|\Exception
     */
    public function afterConstructor(MethodInvocation $constructorInvocation)
    {
        $that = $constructorInvocation->getThis();

        array_map(
            function (ReflectionProperty $property) use ($that) {
                $property->setAccessible(true);

                $this->checkProperty($property, $property->getValue($that));
            },
            $constructorInvocation
                ->getMethod()
                ->getDeclaringClass()
                ->getProperties()
        );

        return $constructorInvocation->proceed();
    }

    /**
     * @param ReflectionProperty $property
     * @param mixed              $value
     *
     * @return void
     *
     * @throws \Exception|\ErrorException
     */
    private function checkProperty(
        ReflectionProperty $property,
        $value
    ) {
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
            (new PropertyTypeFinder())->__invoke($property),
            $value
        );
    }
}
