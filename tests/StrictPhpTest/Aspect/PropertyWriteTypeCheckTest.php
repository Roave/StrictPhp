<?php

namespace StrictPhpTest\Aspect;

use Go\Aop\Intercept\FieldAccess;
use ReflectionProperty;
use StrictPhp\Aspect\PropertyWriteTypeCheck;
use StrictPhpTestAsset\ClassWithGenericNonTypedProperty;
use StrictPhpTestAsset\ClassWithGenericStringTypedProperty;

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
        $fieldAccess = $this->getMock(FieldAccess::class);

        $fieldAccess->expects($this->any())->method('getAccessType')->will($this->returnValue(FieldAccess::WRITE));
        $fieldAccess->expects($this->any())->method('getField')->will($this->returnValue(new ReflectionProperty(
            ClassWithGenericNonTypedProperty::class,
            'property'
        )));
        $fieldAccess->expects($this->once())->method('proceed');

        $immutablePropertyCheck = new PropertyWriteTypeCheck();
        $immutablePropertyCheck->beforePropertyAccess($fieldAccess);
    }
}
