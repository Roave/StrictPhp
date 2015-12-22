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
use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation as Go;

final class PostConstructAspect implements Aspect
{
    /**
     * @var callable[]
     */
    private $stateCheckers;

    /**
     * @param callable[] $stateCheckers
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
        $that  = $constructorInvocation->getThis();
        $scope = $constructorInvocation->getMethod()->getDeclaringClass()->getName();

        array_map(
            function (callable $checker) use ($that, $scope) {
                $checker($that, $scope);
            },
            $this->stateCheckers
        );

        return $constructorInvocation->proceed();
    }
}
