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

use Go\Aop\Intercept\FieldAccess;
use StrictPhp\Aspect\PropertyWriteAspect;

/**
 * Tests for {@see \StrictPhp\Aspect\PropertyWriteAspect}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\Aspect\PropertyWriteAspect
 */
class PropertyWriteAspectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FieldAccess|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fieldAccess;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject[]|callable[]
     */
    private $callables = [];

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->fieldAccess = $this->getMock(FieldAccess::class);
        $this->callables   = [
            $this->getMock('stdClass', ['__invoke']),
            $this->getMock('stdClass', ['__invoke']),
        ];
    }

    public function testWillSkipExecutionWhenNotWriteAccess()
    {
        $this->fieldAccess->expects($this->any())->method('proceed')->will($this->returnValue('done'));
        $this->fieldAccess->expects($this->once())->method('getAccessType')->will($this->returnValue(FieldAccess::READ));

        foreach ($this->callables as $callable) {
            $callable->expects($this->never())->method('__invoke');
        }

        $this->assertSame(
            'done',
            (new PropertyWriteAspect(...$this->callables))->beforePropertyAccess($this->fieldAccess)
        );
    }

    public function testWillExecuteOnWriteAccess()
    {
        $this->fieldAccess->expects($this->once())->method('proceed')->will($this->returnValue('done'));
        $this->fieldAccess->expects($this->once())->method('getAccessType')->will($this->returnValue(FieldAccess::WRITE));


        foreach ($this->callables as $callable) {
            $callable->expects($this->once())->method('__invoke')->with($this->fieldAccess);
        }

        $this->assertSame(
            'done',
            (new PropertyWriteAspect(...$this->callables))->beforePropertyAccess($this->fieldAccess)
        );
    }

    public function testWillNotProceedExecuteOnWriteAndCrash()
    {
        $this->fieldAccess->expects($this->never())->method('proceed')->will($this->returnValue('done'));
        $this->fieldAccess->expects($this->once())->method('getAccessType')->will($this->returnValue(FieldAccess::WRITE));

        foreach ($this->callables as $callable) {
            $callable->expects($this->any())->method('__invoke')->will($this->throwException(new \Exception()));
        }

        $aspect = new PropertyWriteAspect(...$this->callables);

        $this->setExpectedException(\Exception::class);

        $aspect->beforePropertyAccess($this->fieldAccess);
    }
}
