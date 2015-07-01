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

namespace StrictPhpTest\Aspect;

use Go\Aop\Intercept\MethodInvocation;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Object_;
use ReflectionMethod;
use stdClass;
use StrictPhp\AccessChecker\ParameterTypeChecker;
use StrictPhpTestAsset\ClassWithMultipleParamsTypedMethodAnnotation;
use StrictPhpTestAsset\ClassWithVariadicInterfaceParameters;
use StrictPhpTestAsset\HelloInterface;

/**
 * Tests for {@see \StrictPhp\AccessChecker\ParameterTypeChecker}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\AccessChecker\ParameterTypeChecker
 */
class ParameterTypeCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ParameterTypeChecker
     */
    private $parameterCheck;

    /**
     * @var callable|\PHPUnit_Framework_MockObject_MockObject
     */
    private $applyTypeChecks;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->applyTypeChecks = $this->getMock(stdClass::class, ['__invoke']);
        $this->parameterCheck  = new ParameterTypeChecker($this->applyTypeChecks);
    }

    public function testParameterTypeChecker()
    {
        /* @var MethodInvocation|\PHPUnit_Framework_MockObject_MockObject $method */
        $method = $this->getMock(MethodInvocation::class);

        $reflectionMethod = new ReflectionMethod(ClassWithMultipleParamsTypedMethodAnnotation::class, 'method');

        $method->expects($this->once())->method('getMethod')->willReturn($reflectionMethod);
        $method->expects($this->once())->method('getArguments')->willReturn(['foo', 'bar']);

        $parameterCheck = $this->parameterCheck;

        $this
            ->applyTypeChecks
            ->expects($this->exactly(2))
            ->method('__invoke')
            ->with(
                $this->logicalOr(
                    $this->callback(function (array $types) {
                        return (bool) array_map(
                            function (Type $type) {
                                $this->assertInstanceOf(Array_::class, $type);
                            },
                            $types
                        );
                    }),
                    $this->callback(function (array $types) {
                        return (bool) array_map(
                            function (Type $type) {
                                $this->assertInstanceOf(Boolean::class, $type);
                            },
                            $types
                        );
                    })
                ),
                $this->logicalOr('foo', 'bar')
            );

        $parameterCheck($method);
    }

    public function testParameterTypeCheckerWithVariadicArgument()
    {
        /* @var MethodInvocation|\PHPUnit_Framework_MockObject_MockObject $method */
        $method = $this->getMock(MethodInvocation::class);

        $reflectionMethod = new ReflectionMethod(
            ClassWithVariadicInterfaceParameters::class,
            'methodWithParameterAndVariadicSingleParameter'
        );

        $method->expects($this->once())->method('getMethod')->willReturn($reflectionMethod);
        $method->expects($this->once())->method('getArguments')->willReturn(['foo', 'bar', 'baz', 'tab']);

        $parameterCheck = $this->parameterCheck;

        $variadicType   = new Object_(new Fqsen('\\' . HelloInterface::class));

        $this
            ->applyTypeChecks
            ->expects($this->exactly(4))
            ->method('__invoke')
            ->with(
                $this->equalTo([$variadicType]),
                $this->logicalOr('foo', 'bar', 'baz', 'tab')
            );

        $parameterCheck($method);
    }
}
