<?php

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
