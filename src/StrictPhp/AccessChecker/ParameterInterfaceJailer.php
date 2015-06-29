<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

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

            if (! $interface = $this->getParameterInterfaceType($parameterIndex, $method)) {
                continue;
            }

            $argument = $this->jailFactory->createInstanceJail($argument, $interface);
        }
    }

    /**
     * @param int                   $index
     * @param ReflectionMethod|null $reflectionMethod
     *
     * @return ReflectionParameter
     */
    private function getParameterInterfaceType($index, ReflectionMethod $reflectionMethod = null)
    {
        $parameters = $reflectionMethod ? $reflectionMethod->getParameters() : [];

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
