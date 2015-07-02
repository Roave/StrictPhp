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

use Go\Aop\Intercept\MethodInvocation;
use phpDocumentor\Reflection\Type;
use StrictPhp\TypeFinder\ReturnTypeFinder;

/**
 * @author Jefersson Nathan <malukenho@phpse.net>
 */
final class ReturnTypeChecker
{
    /**
     * @var callable
     */
    private $applyTypeChecks;

    /**
     * @param callable $applyTypeChecks
     */
    public function __construct(callable $applyTypeChecks)
    {
        $this->applyTypeChecks = $applyTypeChecks;
    }

    /**
     * @param MethodInvocation $methodInvocation
     *
     * @return void
     *
     * @throws \ErrorException
     */
    public function __invoke(MethodInvocation $methodInvocation)
    {
        $reflectionMethod = $methodInvocation->getMethod();
        $applyTypeChecks  = $this->applyTypeChecks;

        $applyTypeChecks(
            $this->getReturnDocblockType(
                get_class($methodInvocation->getThis()),
                $reflectionMethod
            ),
            $methodInvocation->proceed()
        );
    }

    /**
     * @param $contextClass
     * @param $reflectionMethod
     *
     * @return Type
     */
    private function getReturnDocblockType($contextClass,  $reflectionMethod)
    {
        return (new ReturnTypeFinder())
            ->__invoke($reflectionMethod, $contextClass);
    }
}
