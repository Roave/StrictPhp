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
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\String_;
use ReflectionMethod;
use stdClass;
use StrictPhp\AccessChecker\ParameterTypeChecker;
use StrictPhp\AccessChecker\ReturnTypeChecker;
use StrictPhpTestAsset\ClassWithReturnTypeMethod;

/**
 * Tests for {@see \StrictPhp\AccessChecker\ReturnTypeChecker}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\AccessChecker\ReturnTypeChecker
 */
class ReturnTypeCheckerTest extends \PHPUnit_Framework_TestCase
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
        $this->parameterCheck  = new ReturnTypeChecker($this->applyTypeChecks);
    }

    public function testReturnSimpleTypeChecker()
    {
        /* @var MethodInvocation|\PHPUnit_Framework_MockObject_MockObject $method */
        $method = $this->getMock(MethodInvocation::class);

        $reflectionMethod = new ReflectionMethod(ClassWithReturnTypeMethod::class, 'expectString');

        $method->expects($this->exactly(2))->method('getMethod')->willReturn($reflectionMethod);

        $parameterCheck = $this->parameterCheck;

        $this
            ->applyTypeChecks
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                $this->callback(function (array $types) {
                    return (bool) array_map(
                        function (Type $type) {
                            $this->assertInstanceOf(String_::class, $type);
                        },
                        $types
                    );
                })
            );

        $parameterCheck($method);
    }

    public function testReturnCompostTypeChecker()
    {
        /* @var MethodInvocation|\PHPUnit_Framework_MockObject_MockObject $method */
        $method = $this->getMock(MethodInvocation::class);

        $reflectionMethod = new ReflectionMethod(ClassWithReturnTypeMethod::class, 'expectMixedDataCollection');

        $method->expects($this->exactly(2))->method('getMethod')->willReturn($reflectionMethod);

        $expected = [
            new Array_(new Array_(new String_())),
            new Array_(new Boolean()),
        ];

        $this
            ->applyTypeChecks
            ->expects($this->once())
            ->method('__invoke')
            ->with(
                $this->equalTo($expected)
            );

        $parameterCheck = $this->parameterCheck;

        $parameterCheck($method);
    }
}
