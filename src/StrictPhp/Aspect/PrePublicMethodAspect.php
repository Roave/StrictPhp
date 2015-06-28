<?php

namespace StrictPhp\Aspect;

use Go\Aop\Aspect;
use Go\Aop\Framework\AbstractMethodInvocation;
use Go\Lang\Annotation as Go;

final class PrePublicMethodAspect implements Aspect
{
    /**
     * @var callable[]
     */
    private $interceptors;

    /**
     * @param callable ...$stateCheckers
     */
    public function __construct(callable ...$interceptors)
    {
        $this->interceptors = $interceptors;
    }

    /**
     * @Go\Before("execution(public **->*(*))")
     *
     * @param AbstractMethodInvocation $methodInvocation
     *
     * @return mixed
     */
    public function prePublicMethod(AbstractMethodInvocation $methodInvocation)
    {
        foreach ($this->interceptors as $interceptor) {
            $interceptor($methodInvocation);
        }

        return $methodInvocation->proceed();
    }
}
