<?php

namespace StrictPhpTest\Aspect;

use Go\Aop\Framework\AbstractMethodInvocation;
use Go\Aop\Intercept\MethodInvocation;
use StrictPhp\Aspect\PrePublicMethodAspect;

/**
 * Tests for {@see \StrictPhp\Aspect\PrePublicMethodAspect}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\Aspect\PrePublicMethodAspect
 */
class PrePublicMethodAspectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MethodInvocation|\PHPUnit_Framework_MockObject_MockObject
     */
    private $methodInvocation;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject[]|callable[]
     */
    private $callables = [];

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->methodInvocation = $this->getMock(MethodInvocation::class);
        $this->callables        = [
            $this->getMock('stdClass', ['__invoke']),
            $this->getMock('stdClass', ['__invoke']),
        ];
    }

    public function testWillExecuteAllInterceptorsOnCall()
    {
        /* @var $methodInvocation AbstractMethodInvocation|\PHPUnit_Framework_MockObject_MockObject */
        $methodInvocation = $this->getMockForAbstractClass(AbstractMethodInvocation::class, [], '', false);

        /* @var $callables callable[]|\PHPUnit_Framework_MockObject_MockObject[] */
        $callables = [
            $this->getMock('stdClass', ['__invoke']),
            $this->getMock('stdClass', ['__invoke']),
            $this->getMock('stdClass', ['__invoke']),
        ];

        foreach ($callables as $callable) {
            $callable->expects($this->once())->method('__invoke')->with($methodInvocation);
        }

        $aspect = new PrePublicMethodAspect(...$callables);

        $methodInvocation->expects($this->once())->method('proceed')->will($this->returnValue('result'));

        $this->assertSame('result', $aspect->prePublicMethod($methodInvocation));
    }
}
