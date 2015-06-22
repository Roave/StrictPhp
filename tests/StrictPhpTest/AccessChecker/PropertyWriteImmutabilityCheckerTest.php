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

namespace StrictPhpTest\AccessChecker;

use Go\Aop\Intercept\FieldAccess;
use ReflectionProperty;
use StdClass;
use StrictPhp\AccessChecker\PropertyWriteImmutabilityChecker;
use StrictPhpTestAsset\ClassWithImmutableProperty;

/**
 * Tests for {@see \StrictPhp\AccessChecker\PropertyWriteImmutabilityChecker}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\AccessChecker\PropertyWriteImmutabilityChecker
 */
class PropertyWriteImmutabilityCheckerTest extends \PHPUnit_Framework_TestCase
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
        $this->fieldAccess->expects($this->once())->method('getThis')->will($this->returnValue(null));

        $immutablePropertyCheck = new PropertyWriteImmutabilityChecker();
        $immutablePropertyCheck->__invoke($this->fieldAccess);
    }

    public function testReturnProceedWhenFieldAccessIsNotWritable()
    {
        $this->fieldAccess->expects($this->once())->method('getThis')->willReturnSelf();
        $this->fieldAccess->expects($this->once())->method('getAccessType')->will($this->returnValue(FieldAccess::READ));

        $immutablePropertyCheck = new PropertyWriteImmutabilityChecker();
        $immutablePropertyCheck->__invoke($this->fieldAccess);
    }

    public function testReturnProceedWhenFieldValueIsNull()
    {
        $field = $this->getMockBuilder(StdClass::class)->setMethods(['setAccessible', 'getValue'])->getMock();

        $field->expects($this->once())->method('setAccessible')->willReturnSelf();
        $field->expects($this->once())->method('getValue')->will($this->returnValue(null));

        $this->fieldAccess->expects($this->once())->method('getThis')->willReturnSelf();
        $this->fieldAccess->expects($this->once())->method('getAccessType')->will($this->returnValue(FieldAccess::WRITE));
        $this->fieldAccess->expects($this->once())->method('getField')->will($this->returnValue($field));

        $immutablePropertyCheck = new PropertyWriteImmutabilityChecker();
        $immutablePropertyCheck->__invoke($this->fieldAccess);
    }

    public function testReturnProceedWhenNotFindImmutableTagName()
    {
        $field = $this->getMockBuilder(StdClass::class)->setMethods(['setAccessible', 'getValue', 'getDocComment'])->getMock();

        $field->expects($this->once())->method('setAccessible')->willReturnSelf();
        $field->expects($this->once())->method('getValue')->will($this->returnValue(true));
        $field->expects($this->once())->method('getDocComment')->will($this->returnValue(null));

        $this->fieldAccess->expects($this->once())->method('getThis')->willReturnSelf();
        $this->fieldAccess->expects($this->once())->method('getAccessType')->will($this->returnValue(FieldAccess::WRITE));
        $this->fieldAccess->expects($this->once())->method('getField')->will($this->returnValue($field));

        $immutablePropertyCheck = new PropertyWriteImmutabilityChecker();
        $immutablePropertyCheck->__invoke($this->fieldAccess);
    }

    public function testRaisesExceptionWhenFieldAccessIsInvalid()
    {
        $object = new ClassWithImmutableProperty();
        $field  = new ReflectionProperty(ClassWithImmutableProperty::class, 'property');

        $object->property = 'initialized';

        $this->fieldAccess->expects($this->any())->method('getThis')->will($this->returnValue($object));
        $this->fieldAccess->expects($this->any())->method('getAccessType')->will($this->returnValue(FieldAccess::WRITE));
        $this->fieldAccess->expects($this->any())->method('getField')->will($this->returnValue($field));

        $immutablePropertyCheck = new PropertyWriteImmutabilityChecker();

        $this->setExpectedException(\RuntimeException::class);
        $immutablePropertyCheck->__invoke($this->fieldAccess);
    }
}
