<?php

namespace StrictPhp\Aspect;

use Go\Aop\Intercept\FieldAccess;
use StdClass;

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
        /* @var $fieldAccess FieldAccess|\PHPUnit_Framework_MockObject_MockObject */
        $fieldAccess = $this->getMock(FieldAccess::class);
        $field       = $this->getMockBuilder(StdClass::class)
            ->setMethods(
                [
                    'setAccessible',
                    'getName',
                    'getValue',
                    'getDocComment',
                    'getDeclaringClass'
                ]
            )->getMock();

        $field->expects($this->once())->method('setAccessible')->willReturnSelf();
        $field->expects($this->once())->method('getValue')->will($this->returnValue(true));
        $field->expects($this->once())->method('getDocComment')->will($this->returnValue('/** @immutable */'));
        $field->expects($this->once())->method('getDeclaringClass')->willReturnSelf();
        $field->expects($this->any())->method('getName')->will($this->returnValue(StdClass::class));

        $fieldAccess->expects($this->once())->method('getThis')->willReturnSelf();
        $fieldAccess->expects($this->once())->method('getAccessType')->will($this->returnValue(FieldAccess::WRITE));
        $fieldAccess->expects($this->once())->method('getField')->will($this->returnValue($field));

        $immutablePropertyCheck = new ImmutablePropertyCheck();

        $this->setExpectedException(\RuntimeException::class);
        $immutablePropertyCheck->beforePropertyAccess($fieldAccess);
    }
}
