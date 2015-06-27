<?php

namespace StrictPhp\Aspect;

use Go\Aop\Aspect;
use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation as Go;

final class PostConstructAspect implements Aspect
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
     * @Go\After("execution(public **->__construct(*))")
     *
     * @param MethodInvocation $constructorInvocation
     *
     * @return mixed
     *
     * @throws \ErrorException|\Exception
     */
    public function postConstruct(MethodInvocation $constructorInvocation)
    {
        $that = $constructorInvocation->getThis();

        array_map(
            function (callable $checker) use ($that) {
                $checker($that);
            },
            $this->stateCheckers
        );

        return $constructorInvocation->proceed();
    }
}
