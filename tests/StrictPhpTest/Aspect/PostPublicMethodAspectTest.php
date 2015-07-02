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
use Go\Aop\Intercept\MethodInvocation;
use StrictPhp\Aspect\PostPublicMethodAspect;

/**
 * Tests for {@see \StrictPhp\Aspect\PostPublicMethodAspect}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\Aspect\PostPublicMethodAspect
 */
class PostPublicMethodAspectTest extends \PHPUnit_Framework_TestCase
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

        $aspect = new PostPublicMethodAspect(...$callables);

        $methodInvocation->expects($this->once())->method('proceed')->will($this->returnValue('result'));

        $this->assertSame('result', $aspect->postPublicMethod($methodInvocation));
    }
}
