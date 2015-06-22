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

use Go\Aop\Framework\AbstractMethodInvocation;
use InterNations\Component\TypeJail\Factory\JailFactoryInterface;
use ReflectionMethod;
use ReflectionProperty;
use StrictPhp\AccessChecker\ParameterInterfaceJailer;
use StrictPhpTestAsset\ClassWithVariadicInterfaceParameters;

/**
 * Tests for {@see \StrictPhp\AccessChecker\ParameterInterfaceJailer}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\AccessChecker\ParameterInterfaceJailer
 */
class ParameterInterfaceJailerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JailFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $jailFactory;

    /**
     * @var ParameterInterfaceJailer
     */
    private $jailer;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->jailFactory = $this->getMock(JailFactoryInterface::class);
        $this->jailer      = new ParameterInterfaceJailer($this->jailFactory);
    }

    /**
     * @dataProvider jailExpectationParameters
     *
     * @param string $class
     * @param string $method
     * @param array  $parameters
     * @param array  $expectedJails
     */
    public function testJailingWithParameters($class, $method, array $parameters, array $expectedJails)
    {
        /* @var $methodInvocation AbstractMethodInvocation|\PHPUnit_Framework_MockObject_MockObject */
        $methodInvocation = $this->getMockForAbstractClass(AbstractMethodInvocation::class, [], '', false);
        $invocationArgs   = new ReflectionProperty(AbstractMethodInvocation::class, 'arguments');
        $invocationMethod = new ReflectionProperty(AbstractMethodInvocation::class, 'reflectionMethod');

        $invocationArgs->setAccessible(true);
        $invocationMethod->setAccessible(true);
        $invocationArgs->setValue($methodInvocation, $parameters);
        $invocationMethod->setValue($methodInvocation, new ReflectionMethod($class, $method));

        $this
            ->jailFactory
            ->expects($this->exactly(count($expectedJails)))
            ->method('createInstanceJail')
            ->with($this->logicalOr(...array_intersect_key($parameters, $expectedJails)))
            ->will($this->returnCallback(function ($value) use ($expectedJails, $parameters) {
                return $expectedJails[array_search($value, $parameters, true)];
            }));

        $jailer = $this->jailer;

        $jailer($methodInvocation);

        $jailedParameters = $parameters;

        foreach ($expectedJails as $key => $jailed) {
            $jailedParameters[$key] = $jailed;
        }

        $this->assertSame($jailedParameters, $invocationArgs->getValue($methodInvocation));
    }

    public function testWillSkipWhenReflectionMethodIsUnavailable()
    {
        $parameters       = [new \stdClass(), new \stdClass()];
        /* @var $methodInvocation AbstractMethodInvocation|\PHPUnit_Framework_MockObject_MockObject */
        $methodInvocation = $this->getMockForAbstractClass(AbstractMethodInvocation::class, [], '', false);
        $invocationArgs   = new ReflectionProperty(AbstractMethodInvocation::class, 'arguments');

        $invocationArgs->setAccessible(true);
        $invocationArgs->setValue($methodInvocation, $parameters);

        $this
            ->jailFactory
            ->expects($this->never())
            ->method('createInstanceJail');

        $this->jailer->__invoke($methodInvocation);

        $this->assertSame($parameters, $invocationArgs->getValue($methodInvocation));
    }

    /**
     * Data provider
     *
     * @return string[][]|mixed[][]
     */
    public function jailExpectationParameters()
    {
        $someObject1     = new \stdClass();
        $someObject2     = new \stdClass();
        $someObject3     = new \stdClass();
        $someObject4     = new \stdClass();
        $someObjectJail1 = new \stdClass();
        $someObjectJail2 = new \stdClass();
        $someObjectJail3 = new \stdClass();
        $someObjectJail4 = new \stdClass();

        return [
            'no parameters' => [
                ClassWithVariadicInterfaceParameters::class,
                'methodWithNoParameters',
                [],
                [],
            ],
            'more parameters than which are defined in the method' => [
                ClassWithVariadicInterfaceParameters::class,
                'methodWithNoParameters',
                ['foo'],
                [],
            ],
            'more (compatible) parameter than which are defined in the method' => [
                ClassWithVariadicInterfaceParameters::class,
                'methodWithNoParameters',
                [$someObject1],
                [],
            ],
            'call to a method with an interfaced hint' => [
                ClassWithVariadicInterfaceParameters::class,
                'methodWithSingleParameter',
                [$someObject1],
                [$someObjectJail1],
            ],
            'call to a method with an interfaced hint (and additional undefined parameter)' => [
                ClassWithVariadicInterfaceParameters::class,
                'methodWithSingleParameter',
                [$someObject1, 'foo'],
                [$someObjectJail1],
            ],
            'call to a method with variadic parameters (0 parameters given)' => [
                ClassWithVariadicInterfaceParameters::class,
                'methodWithVariadicSingleParameter',
                [],
                [],
            ],
            'call to a method with variadic parameters (many parameters given)' => [
                ClassWithVariadicInterfaceParameters::class,
                'methodWithVariadicSingleParameter',
                [$someObject1, $someObject2, $someObject3, $someObject4],
                [$someObjectJail1, $someObjectJail2, $someObjectJail3, $someObjectJail4],
            ],
            'call to a method with an interfaced parameter and a variadic interfaced parameter (1 parameter)' => [
                ClassWithVariadicInterfaceParameters::class,
                'methodWithParameterAndVariadicSingleParameter',
                [$someObject1],
                [$someObjectJail1],
            ],
            'call to a method with an interfaced parameter and a variadic interfaced parameter (2 parameters)' => [
                ClassWithVariadicInterfaceParameters::class,
                'methodWithParameterAndVariadicSingleParameter',
                [$someObject1, $someObject2],
                [$someObjectJail1, $someObjectJail2],
            ],
            'call to a method with an interfaced parameter and a variadic interfaced parameter (3 parameters)' => [
                ClassWithVariadicInterfaceParameters::class,
                'methodWithParameterAndVariadicSingleParameter',
                [$someObject1, $someObject2, $someObject3],
                [$someObjectJail1, $someObjectJail2, $someObjectJail3],
            ],
            'call to a method with an interfaced and a non interfaced parameter' => [
                ClassWithVariadicInterfaceParameters::class,
                'methodWithInterfacedAndNonInterfacedParameters',
                [$someObject1, $someObject2],
                [$someObjectJail1],
            ],
            'call to a method with an non interfaced and an interfaced parameter' => [
                ClassWithVariadicInterfaceParameters::class,
                'methodWithNonInterfacedAndInterfacedParameters',
                [$someObject1, $someObject2],
                [1 => $someObjectJail2],
            ],
            'call to a method with an concrete type hint and an interfaced parameter' => [
                ClassWithVariadicInterfaceParameters::class,
                'methodWithConcreteTypeHintAndInterfaceHint',
                [$someObject1, $someObject2],
                [1 => $someObjectJail2],
            ],
            'call to a method with a "null" default interface hint' => [
                ClassWithVariadicInterfaceParameters::class,
                'methodWithInterfacedHintAndDefaultNull',
                [null],
                [],
            ],
        ];
    }
}
