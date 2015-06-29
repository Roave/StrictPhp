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
use ReflectionMethod;
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
        $this
            ->methodInvocation
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue(new ReflectionMethod(__CLASS__, __FUNCTION__)));


        foreach ($this->callables as $callable) {
            $callable->expects($this->once())->method('__invoke')->with($object, __CLASS__);
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
        $this
            ->methodInvocation
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue(new ReflectionMethod(__CLASS__, __FUNCTION__)));

        foreach ($this->callables as $callable) {
            $callable
                ->expects($this->any())
                ->method('__invoke')
                ->with($object, __CLASS__)
                ->will($this->throwException(new \Exception()));
        }

        $aspect = new PostConstructAspect(...$this->callables);

        $this->setExpectedException(\Exception::class);

        $aspect->postConstruct($this->methodInvocation);
    }
}
