<?php

namespace StrictPhp\TypeChecker\TypeChecker;

use DateTime;
use StdClass;

/**
 * Tests for {@see \StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 */
class ObjectTypeCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectTypeChecker
     */
    private $objectTypeCheck;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->objectTypeCheck = new ObjectTypeChecker();
    }

    /**
     * @covers       \StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker::canApplyToType
     *
     * @dataProvider mixedDataTypes
     *
     * @param string  $type
     * @param boolean $expected
     */
    public function testTypeCanBeApplied($type, $expected)
    {
        $this->assertSame($expected, $this->objectTypeCheck->canApplyToType($type));
    }

    /**
     * @covers        \StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker::validate
     *
     * @dataProvider  mixedDataTypesToValidate
     *
     * @param string  $value
     * @param string  $type
     * @param boolean $expected
     */
    public function testIfDataTypeIsValid($value, $type, $expected)
    {
        $this->assertSame($expected, $this->objectTypeCheck->validate($value, $type));
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker::simulateFailure
     */
    public function testSimulateFailureRaisesExceptionWhenNotPassAString()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $this->objectTypeCheck->simulateFailure([], StdClass::class);
        $this->objectTypeCheck->simulateFailure('Marco Pivetta', StdClass::class);
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker::simulateFailure
     */
    public function testSimulateFailureDoesNothingWhenPassAString()
    {
        $this->objectTypeCheck->simulateFailure(new StdClass, StdClass::class);
        $this->objectTypeCheck->simulateFailure(new DateTime, DateTime::class);
    }

    /**
     * @return mixed[][] - mixed data type
     *                   - name of class to tests against
     *                   - boolean expected
     *
     */
    public function mixedDataTypesToValidate()
    {
        return [
            [new StdClass,    StdClass::class, true],
            [new DateTime,    DateTime::class, true],
            [123,             StdClass::class, false],
            [0x12,            StdClass::class, false],
            ['Marco Pivetta', StdClass::class, false],
            [[],              StdClass::class, false],
            [true,            StdClass::class, false],
            [null,            StdClass::class, false],
        ];
    }

    /**
     * @return mixed[][] - string with type of data
     *                   - expected output
     */
    public function mixedDataTypes()
    {
        return [
            [StdClass::class, true],
            ['integer',       false],
            ['integer',       false],
            ['int',           false],
            ['object',        false],
            ['string',        false],
            ['array',         false],
            ['boolean',       false],
            ['bool',          false],
            ['null',          false],
            ['mixed',         false],
        ];
    }
}
