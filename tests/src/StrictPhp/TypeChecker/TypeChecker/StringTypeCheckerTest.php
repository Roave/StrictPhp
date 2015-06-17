<?php

namespace StrictPhp\TypeChecker\TypeChecker;

/**
 * Tests for {@see \StrictPhp\TypeChecker\TypeChecker\StringTypeChecker}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 */
class StringTypeCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StringTypeChecker
     */
    private $stringCheck;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->stringCheck = new StringTypeChecker();
    }

    /**
     * @covers       \StrictPhp\TypeChecker\TypeChecker\StringTypeChecker::canApplyToType
     *
     * @dataProvider mixedDataTypes
     *
     * @param string  $type
     * @param boolean $expected
     */
    public function testTypeCanBeApplied($type, $expected)
    {
        $this->assertSame($expected, $this->stringCheck->canApplyToType($type));
    }

    /**
     * @covers        \StrictPhp\TypeChecker\TypeChecker\StringTypeChecker::validate
     *
     * @dataProvider  mixedDataTypesToValidate
     *
     * @param string  $value
     * @param boolean $expected
     */
    public function testIfDataTypeIsValid($value, $expected)
    {
        $this->assertSame($expected, $this->stringCheck->validate($value, null));
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\StringTypeChecker::simulateFailure
     */
    public function testSimulateFailureRaisesExceptionWhenNotPassAString()
    {
        $this->setExpectedException(\ErrorException::class);
        $this->stringCheck->simulateFailure([], null);
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\StringTypeChecker::simulateFailure
     */
    public function testSimulateFailureDoesNothingWhenPassAString()
    {
        $this->stringCheck->simulateFailure('Marco Pivetta', null);
    }

    /**
     * @return mixed[][] - mixed type
     *                   - boolean expected
     *
     */
    public function mixedDataTypesToValidate()
    {
        return [
            ['Marco Pivetta', true],
            [[],              false],
            [new \StdClass,   false],
            [true,            false],
            [null,            false],
            [123,             false],
            [1e-3,            false],
            [0x12,            false],
        ];
    }

    /**
     * @return mixed[][] - string with type of data
     *                   - expected output
     */
    public function mixedDataTypes()
    {
        return [
            ['string',  true],
            ['array',   false],
            ['object',  false],
            ['boolean', false],
            ['bool',    false],
            ['integer', false],
            ['int',     false],
            ['null',    false],
            ['mixed',   false],
        ];
    }
}

