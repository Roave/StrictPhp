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
use ReflectionProperty;
use StrictPhp\AccessChecker\PropertyWriteTypeChecker;
use StrictPhpTestAsset\ClassWithGenericArrayTypedProperty;

/**
 * Tests for {@see \StrictPhp\AccessChecker\PropertyWriteTypeChecker}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\AccessChecker\PropertyWriteTypeChecker
 */
class PropertyWriteTypeCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FieldAccess|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fieldAccess;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->fieldAccess = $this->getMock(FieldAccess::class);

        $this->fieldAccess->expects($this->never())->method('proceed');
    }

    public function testReturnProceedWhenCannotGetJoinPointObject()
    {
        $immutablePropertyCheck = new PropertyWriteTypeChecker();
        $immutablePropertyCheck->__invoke($this->fieldAccess);
    }

    public function testReturnProceedWhenFieldAccessIsNotAWrite()
    {
        $this->fieldAccess->expects($this->any())->method('getAccessType')->will($this->returnValue(FieldAccess::READ));

        $immutablePropertyCheck = new PropertyWriteTypeChecker();
        $immutablePropertyCheck->__invoke($this->fieldAccess);
    }

    public function testWillFailOnInvalidAssignedType()
    {
        $field = new ReflectionProperty(ClassWithGenericArrayTypedProperty::class, 'property');

        $this->fieldAccess->expects($this->any())->method('getAccessType')->will($this->returnValue(FieldAccess::WRITE));
        $this->fieldAccess->expects($this->any())->method('getField')->will($this->returnValue($field));
        $this->fieldAccess->expects($this->any())->method('getValueToSet')->will($this->returnValue('new value'));

        $immutablePropertyCheck = new PropertyWriteTypeChecker();

        // catching the exception raised by PHPUnit by converting a fatal into an exception (in the error handler)
        $this->setExpectedException(\PHPUnit_Framework_Error::class);

        $immutablePropertyCheck->__invoke($this->fieldAccess);
    }
}
