<?php

namespace StrictPhpTest\Aspect;

use Go\Aop\Intercept\FieldAccess;
use StdClass;
use StrictPhp\Aspect\ImmutablePropertyCheck;
use StrictPhpTestAsset\ClassWithImmutableProperty;
use ReflectionProperty;

/**
 * Tests for {@see \StrictPhp\Aspect\ImmutablePropertyCheck}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 */
class ImmutablePropertyCheckTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \StrictPhp\Aspect\ImmutablePropertyCheck::beforePropertyAccess
     */
    public function testReturnProceedWhenCannotGetJoinPointObject()
    {
        /* @var $fieldAccess FieldAccess|\PHPUnit_Framework_MockObject_MockObject */
        $fieldAccess = $this->getMock(FieldAccess::class);

        $fieldAccess->expects($this->once())->method('getThis')->will($this->returnValue(null));
        $fieldAccess->expects($this->once())->method('proceed');

        $immutablePropertyCheck = new ImmutablePropertyCheck();
        $immutablePropertyCheck->beforePropertyAccess($fieldAccess);
    }

    /**
     * @covers \StrictPhp\Aspect\ImmutablePropertyCheck::beforePropertyAccess
     */
    public function testReturnProceedWhenFieldAccessIsNotWritable()
    {
        /* @var $fieldAccess FieldAccess|\PHPUnit_Framework_MockObject_MockObject */
        $fieldAccess = $this->getMock(FieldAccess::class);

        $fieldAccess->expects($this->once())->method('getThis')->willReturnSelf();
        $fieldAccess->expects($this->once())->method('getAccessType')->will($this->returnValue(FieldAccess::READ));
        $fieldAccess->expects($this->once())->method('proceed');

        $immutablePropertyCheck = new ImmutablePropertyCheck();
        $immutablePropertyCheck->beforePropertyAccess($fieldAccess);
    }

    /**
     * @covers \StrictPhp\Aspect\ImmutablePropertyCheck::beforePropertyAccess
     */
    public function testReturnProceedWhenFieldValueIsNull()
    {
        /* @var $fieldAccess FieldAccess|\PHPUnit_Framework_MockObject_MockObject */
        $fieldAccess = $this->getMock(FieldAccess::class);
        $field       = $this->getMockBuilder(StdClass::class)->setMethods(['setAccessible', 'getValue'])->getMock();

        $field->expects($this->once())->method('setAccessible')->willReturnSelf();
        $field->expects($this->once())->method('getValue')->will($this->returnValue(null));

        $fieldAccess->expects($this->once())->method('getThis')->willReturnSelf();
        $fieldAccess->expects($this->once())->method('getAccessType')->will($this->returnValue(FieldAccess::WRITE));
        $fieldAccess->expects($this->once())->method('getField')->will($this->returnValue($field));
        $fieldAccess->expects($this->once())->method('proceed');

        $immutablePropertyCheck = new ImmutablePropertyCheck();
        $immutablePropertyCheck->beforePropertyAccess($fieldAccess);
    }

    /**
     * @covers \StrictPhp\Aspect\ImmutablePropertyCheck::beforePropertyAccess
     */
    public function testReturnProceedWhenNotFindImmutableTagName()
    {
        /* @var $fieldAccess FieldAccess|\PHPUnit_Framework_MockObject_MockObject */
        $fieldAccess = $this->getMock(FieldAccess::class);
        $field       = $this->getMockBuilder(StdClass::class)->setMethods(['setAccessible', 'getValue', 'getDocComment'])->getMock();

        $field->expects($this->once())->method('setAccessible')->willReturnSelf();
        $field->expects($this->once())->method('getValue')->will($this->returnValue(true));
        $field->expects($this->once())->method('getDocComment')->will($this->returnValue(null));

        $fieldAccess->expects($this->once())->method('getThis')->willReturnSelf();
        $fieldAccess->expects($this->once())->method('getAccessType')->will($this->returnValue(FieldAccess::WRITE));
        $fieldAccess->expects($this->once())->method('getField')->will($this->returnValue($field));
        $fieldAccess->expects($this->once())->method('proceed');

        $immutablePropertyCheck = new ImmutablePropertyCheck();
        $immutablePropertyCheck->beforePropertyAccess($fieldAccess);
    }

    /**
     * @covers \StrictPhp\Aspect\ImmutablePropertyCheck::beforePropertyAccess
     */
    public function testRaisesExceptionWhenFieldAccessIsInvalid()
    {
        $object      = new ClassWithImmutableProperty();
        /* @var $fieldAccess FieldAccess|\PHPUnit_Framework_MockObject_MockObject */
        $fieldAccess = $this->getMock(FieldAccess::class);
        $field       = new ReflectionProperty(ClassWithImmutableProperty::class, 'property');

        $object->property = 'initialized';

        $fieldAccess->expects($this->any())->method('getThis')->will($this->returnValue($object));
        $fieldAccess->expects($this->any())->method('getAccessType')->will($this->returnValue(FieldAccess::WRITE));
        $fieldAccess->expects($this->any())->method('getField')->will($this->returnValue($field));

        $immutablePropertyCheck = new ImmutablePropertyCheck();

        $this->setExpectedException(\RuntimeException::class);
        $immutablePropertyCheck->beforePropertyAccess($fieldAccess);
    }
}
