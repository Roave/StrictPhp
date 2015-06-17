<?php

namespace StrictPhp\TypeChecker\TypeChecker;

/**
 * Tests for {@see \StrictPhp\TypeChecker\TypeChecker\ArrayTypeChecker}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 */
class ArrayTypeCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayTypeChecker
     */
    private $arrayCheck;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->arrayCheck = new ArrayTypeChecker();
    }

    /**
     * @covers       \StrictPhp\TypeChecker\TypeChecker\ArrayTypeChecker::canApplyToType
     *
     * @dataProvider mixedDataTypes
     *
     * @param string  $type
     * @param boolean $expected
     */
    public function testTypeCanBeApplied($type, $expected)
    {
        $this->assertSame($expected, $this->arrayCheck->canApplyToType($type));
    }

    /**
     * @covers        \StrictPhp\TypeChecker\TypeChecker\ArrayTypeChecker::validate
     *
     * @dataProvider  mixedDataTypesToValidate
     *
     * @param string  $value
     * @param boolean $expected
     */
    public function testIfDataTypeIsValid($value, $expected)
    {
        $this->assertSame($expected, $this->arrayCheck->validate($value, null));
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\ArrayTypeChecker::simulateFailure
     */
    public function testSimulateFailure()
    {
        $this->assertAttributeEmpty('failingCallback', $this->arrayCheck);
        $this->arrayCheck->simulateFailure([], null);
        $this->assertAttributeNotEmpty('failingCallback', $this->arrayCheck);
        $this->assertAttributeInternalType('callable', 'failingCallback', $this->arrayCheck);
    }

    /**
     * @return mixed[][] - mixed type
     *                   - boolean expected
     *
     */
    public function mixedDataTypesToValidate()
    {
        return [
            [[],              true],
            [new \StdClass,   false],
            [true,            false],
            [null,            false],
            [123,             false],
            [1e-3,            false],
            [0x12,            false],
            ['Marco Pivetta', false],
        ];
    }

    /**
     * @return mixed[][] - string with type of data
     *                   - expected output
     */
    public function mixedDataTypes()
    {
        return [
            ['array',   true],
            ['string',  false],
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
