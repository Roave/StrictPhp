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

namespace StrictPhp\Aspect;

use Go\Aop\Aspect;
use Go\Aop\Framework\AbstractMethodInvocation;
use Go\Lang\Annotation as Go;
use StrictPhp\Projector\MethodExecutor;

/**
 * @author Jefersson Nathan <malukenho@phpse.net>
 */
final class PostPublicMethodAspect implements Aspect
{
    /**
     * @var callable[]
     */
    private $interceptors;

    /**
     * @param callable[] $interceptors
     */
    public function __construct(callable ...$interceptors)
    {
        $this->interceptors = $interceptors;
    }

    /**
     * @Go\After("execution(public **->*(*))")
     *
     * @param AbstractMethodInvocation $methodInvocation
     *
     * @return mixed
     */
    public function postPublicMethod(AbstractMethodInvocation $methodInvocation)
    {
        $scope      = get_class($methodInvocation->getThis());
        $methodName = $methodInvocation->getMethod()->getName();

        foreach ($this->interceptors as $interceptor) {
            $interceptor($methodInvocation, $scope);
        }

        if (! MethodExecutor::has($methodName)) {
            MethodExecutor::store($methodName, $methodInvocation->proceed());
        }

        return MethodExecutor::retrieve($methodName);
    }
}
