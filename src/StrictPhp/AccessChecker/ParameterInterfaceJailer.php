<?php

namespace StrictPhp\AccessChecker;

use Closure;
use Go\Aop\Framework\AbstractMethodInvocation;
use Go\Lang\Annotation as Go;
use InterNations\Component\TypeJail\Exception\ExceptionInterface;
use InterNations\Component\TypeJail\Exception\HierarchyException;
use InterNations\Component\TypeJail\Factory\JailFactoryInterface;
use ReflectionMethod;
use ReflectionParameter;

final class ParameterInterfaceJailer
{
    /**
     * @var JailFactoryInterface
     */
    private $jailFactory;

    /**
     * @param JailFactoryInterface $jailFactory
     */
    public function __construct(JailFactoryInterface $jailFactory)
    {
        $this->jailFactory = $jailFactory;
    }

    /**
     * Replaces the parameters within the given $methodInvocation with type-safe interface jails, whenever applicable
     *
     * @param AbstractMethodInvocation $methodInvocation
     *
     * @return void
     *
     * @throws ExceptionInterface
     * @throws HierarchyException
     */
    public function __invoke(AbstractMethodInvocation $methodInvocation)
    {
        $method    = $methodInvocation->getMethod();
        $arguments = & Closure::bind(
            function & (AbstractMethodInvocation $methodInvocation) {
                return $methodInvocation->arguments;
            },
            null,
            AbstractMethodInvocation::class
        )->__invoke($methodInvocation);

        foreach ($arguments as $parameterIndex => & $argument) {
            if (null === $argument) {
                continue;
            }

            if (! $interface = $this->getParameterInterfaceType($method, $parameterIndex)) {
                continue;
            }

            $argument = $this->jailFactory->createInstanceJail($argument, $interface);
        }
    }

    /**
     * @param ReflectionMethod $reflectionMethod
     * @param int              $index
     *
     * @return ReflectionParameter
     */
    private function getParameterInterfaceType(ReflectionMethod $reflectionMethod, $index)
    {
        $parameters = $reflectionMethod->getParameters();

        if (isset($parameters[$index])) {
            return $this->getInterface($parameters[$index]);
        }

        /* @var $lastParameter \ReflectionParameter|null */
        $lastParameter = end($parameters);

        return $lastParameter && $lastParameter->isVariadic()
            ? $this->getInterface($lastParameter)
            : null;
    }

    /**
     * @param ReflectionParameter $parameter
     *
     * @return string|null
     */
    private function getInterface(ReflectionParameter $parameter)
    {
        return ($class = $parameter->getClass())
            && $class->isInterface()
            ? $class->getName()
            : null;
    }
}
