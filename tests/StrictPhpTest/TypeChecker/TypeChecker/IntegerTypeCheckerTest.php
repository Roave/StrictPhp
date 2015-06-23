<?php

namespace StrictPhpTest\TypeChecker\TypeChecker;

use StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker;

/**
 * Tests for {@see \StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 */
class IntegerTypeCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IntegerTypeChecker
     */
    private $integerCheck;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->integerCheck = new IntegerTypeChecker();
    }

    /**
     * @covers       \StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker::canApplyToType
     *
     * @dataProvider mixedDataTypes
     *
     * @param string  $type
     * @param boolean $expected
     */
    public function testTypeCanBeApplied($type, $expected)
    {
        $this->assertSame($expected, $this->integerCheck->canApplyToType($type));
    }

    /**
     * @covers        \StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker::validate
     *
     * @dataProvider  mixedDataTypesToValidate
     *
     * @param string  $value
     * @param boolean $expected
     */
    public function testIfDataTypeIsValid($value, $expected)
    {
        $this->assertSame($expected, $this->integerCheck->validate($value, null));
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker::simulateFailure
     */
    public function testSimulateFailureRaisesExceptionWhenNotPassAString()
    {
        $this->setExpectedException(\ErrorException::class);
        $this->integerCheck->simulateFailure([], null);
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker::simulateFailure
     */
    public function testSimulateFailureDoesNothingWhenPassAString()
    {
        $this->integerCheck->simulateFailure(10, null);
    }

    /**
     * @return mixed[][] - mixed type
     *                   - boolean expected
     *
     */
    public function mixedDataTypesToValidate()
    {
        return [
            [123,             true],
            [0x12,            true],
            [new \StdClass,   false],
            ['Marco Pivetta', false],
            [[],              false],
            [true,            false],
            [null,            false],
        ];
    }

    /**
     * @return mixed[][] - string with type of data
     *                   - expected output
     */
    public function mixedDataTypes()
    {
        return [
            ['integer', true],
            ['int',     true],
            ['object',  false],
            ['string',  false],
            ['array',   false],
            ['boolean', false],
            ['bool',    false],
            ['null',    false],
            ['mixed',   false],
        ];
    }
}
