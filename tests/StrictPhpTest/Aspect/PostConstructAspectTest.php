<?php

namespace StrictPhpTest\Aspect;

use Go\Aop\Intercept\MethodInvocation;
use StrictPhp\Aspect\PostConstructAspect;

/**
 * Tests for {@see \StrictPhp\Aspect\PostConstructAspect}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\Aspect\PostConstructAspect
 */
class PostConstructAspectTest extends \PHPUnit_Framework_TestCase
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

    public function testWillExecuteOnWriteAccess()
    {
        $object = new \stdClass();

        $this->methodInvocation->expects($this->once())->method('proceed')->will($this->returnValue('done'));
        $this->methodInvocation->expects($this->any())->method('getThis')->will($this->returnValue($object));


        foreach ($this->callables as $callable) {
            $callable->expects($this->once())->method('__invoke')->with($object);
        }

        $this->assertSame(
            'done',
            (new PostConstructAspect(...$this->callables))->postConstruct($this->methodInvocation)
        );
    }

    public function testWillNotProceedExecuteOnWriteAndCrash()
    {
        $object = new \stdClass();

        $this->methodInvocation->expects($this->never())->method('proceed')->will($this->returnValue('done'));
        $this->methodInvocation->expects($this->any())->method('getThis')->will($this->returnValue($object));

        foreach ($this->callables as $callable) {
            $callable
                ->expects($this->any())
                ->method('__invoke')
                ->with($object)
                ->will($this->throwException(new \Exception()));
        }

        $aspect = new PostConstructAspect(...$this->callables);

        $this->setExpectedException(\Exception::class);

        $aspect->postConstruct($this->methodInvocation);
    }
}
