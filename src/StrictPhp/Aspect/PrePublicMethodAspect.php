<?php

namespace StrictPhp\Aspect;

use Go\Aop\Aspect;
use Go\Lang\Annotation as Go;
use Go\Aop\Framework\AbstractMethodInvocation;
use InterNations\Component\TypeJail\Factory\JailFactory;

final class PrePublicMethodAspect implements Aspect
{
    /**
     * @var callable[]
     */
    private $stateCheckers;

    /**
     * @param callable ...$stateCheckers
     */
    public function __construct(callable ...$stateCheckers)
    {
        $this->stateCheckers = $stateCheckers;
    }

    /**
     * @Go\Before("execution(public **->*(*))", scope=AbstractMethodInvocation::class)
     *
     * @param AbstractMethodInvocation $methodInvocation
     *
     * @return mixed
     */
    public function postConstruct(AbstractMethodInvocation $methodInvocation)
    {
        // following line is executed in the scope of AbstractMethodInvocation
        $arguments = & $methodInvocation->arguments; // UNSAFE

        foreach ($methodInvocation->getMethod()->getParameters() as $parameterIndex => $parameter) {
            if (! $class = $parameter->getClass()) {
                continue;
            }

            if (null === $arguments[$parameterIndex] && $parameter->isOptional()) {
                continue;
            }

            if (! $class->isInterface()) {
                continue;
            }

            $arguments[$parameterIndex] = (new JailFactory())
                ->createInstanceJail($arguments[$parameterIndex], $class->getName());
        }

        return $methodInvocation->proceed();
    }
}
