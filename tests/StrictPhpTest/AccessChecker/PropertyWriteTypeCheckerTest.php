<?php

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
