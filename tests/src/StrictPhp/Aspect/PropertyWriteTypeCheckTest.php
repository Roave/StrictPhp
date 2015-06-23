<?php

namespace StrictPhpTest\Aspect;

use Go\Aop\Intercept\FieldAccess;
use ReflectionProperty;
use StrictPhp\Aspect\PropertyWriteTypeCheck;

/**
 * Tests for {@see \StrictPhp\Aspect\PropertyWriteTypeCheck}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 */
class PropertyWriteTypeCheckTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \StrictPhp\Aspect\PropertyWriteTypeCheck::beforePropertyAccess
     */
    public function testReturnProceedWhenFieldAccessIsNotWritable()
    {
        /* @var $fieldAccess FieldAccess|\PHPUnit_Framework_MockObject_MockObject */
        $fieldAccess = $this->getMock(FieldAccess::class);
        $fieldAccess->expects($this->once())->method('getAccessType')->will($this->returnValue(FieldAccess::READ));
        $fieldAccess->expects($this->once())->method('proceed');

        $immutablePropertyCheck = new PropertyWriteTypeCheck();
        $immutablePropertyCheck->beforePropertyAccess($fieldAccess);
    }
    
    /**
     * @covers \StrictPhp\Aspect\PropertyWriteTypeCheck::beforePropertyAccess
     */
    public function testCanApplyTypeCheckAndCallProceed()
    {
        /* @var $fieldAccess FieldAccess|\PHPUnit_Framework_MockObject_MockObject */
        $fieldAccess        = $this->getMock(FieldAccess::class);
        /* @var $reflectionProperty ReflectionProperty|\PHPUnit_Framework_MockObject_MockObject */
        $reflectionProperty = $this->getMockBuilder(ReflectionProperty::class)->disableOriginalConstructor()->getMock();

        $fieldAccess->expects($this->once())->method('getAccessType')->will($this->returnValue(FieldAccess::WRITE));
        $fieldAccess->expects($this->once())->method('getField')->will($this->returnValue($reflectionProperty));
        $fieldAccess->expects($this->once())->method('proceed');

        $immutablePropertyCheck = new PropertyWriteTypeCheck();
        $immutablePropertyCheck->beforePropertyAccess($fieldAccess);
    }
}
