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

use ReflectionMethod;
use ReflectionProperty;
use StrictPhp\AccessChecker\ParameterTypeChecker;
use Go\Aop\Intercept\MethodInvocation;

/**
 * Tests for {@see \StrictPhp\AccessChecker\ParameterTypeCheckerTest}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\AccessChecker\ParameterTypeCheckerTest
 */
class ParameterTypeCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ParameterTypeChecker
     */
    private $parameterCheck;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->parameterCheck = new ParameterTypeChecker();
    }

    public function testParameterTypeChecker()
    {
        /** @var MethodInvocation|\PHPUnit_Framework_MockObject_MockObject $method */
        $method = $this->getMock(MethodInvocation::class);

        /** @var ReflectionMethod|\PHPUnit_Framework_MockObject_MockObject $reflectionMethod */
        $reflectionMethod = $this->getMockBuilder(ReflectionMethod::class)->disableOriginalConstructor()->getMock();
        /** @var ReflectionProperty|\PHPUnit_Framework_MockObject_MockObject $propertyOne */
        $propertyOne = $this->getMockBuilder(ReflectionProperty::class)->disableOriginalConstructor()->getMock();
        /** @var ReflectionProperty|\PHPUnit_Framework_MockObject_MockObject $propertyTwo */
        $propertyTwo = $this->getMockBuilder(ReflectionProperty::class)->disableOriginalConstructor()->getMock();

        $propertyOne->expects($this->once())->method('getName')->willReturn('parameterOne');
        $propertyTwo->expects($this->once())->method('getName')->willReturn('parameterTwo');

        $properties = [
            $propertyOne,
            $propertyTwo,
        ];

        $reflectionMethod->expects($this->once())->method('getNamespaceName')->willReturn('Application');
        $reflectionMethod->expects($this->once())->method('getParameters')->willReturn($properties);
        $reflectionMethod->expects($this->once())->method('getDeclaringClass')->willReturnSelf();

        $method->expects($this->once())->method('getMethod')->willReturn($reflectionMethod);
        $method->expects($this->once())->method('getArguments')->willReturn($properties);

        $parameterCheck = $this->parameterCheck;
        $parameterCheck($method);
    }
}
