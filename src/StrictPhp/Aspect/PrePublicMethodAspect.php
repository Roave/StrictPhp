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
