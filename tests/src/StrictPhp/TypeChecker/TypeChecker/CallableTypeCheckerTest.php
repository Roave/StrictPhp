<?php

namespace StrictPhpTest\TypeChecker\TypeChecker;

use StrictPhp\TypeChecker\TypeChecker\CallableTypeChecker;

/**
 * Tests for {@see \StrictPhp\TypeChecker\TypeChecker\CallableTypeChecker}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 */
class CallableTypeCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CallableTypeChecker
     */
    private $callableCheck;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->callableCheck = new CallableTypeChecker();
    }

    /**
     * @covers       \StrictPhp\TypeChecker\TypeChecker\CallableTypeChecker::canApplyToType
     *
     * @dataProvider mixedDataTypes
     *
     * @param string  $type
     * @param boolean $expected
     */
    public function testTypeCanBeApplied($type, $expected)
    {
        $this->assertSame($expected, $this->callableCheck->canApplyToType($type));
    }

    /**
     * @covers        \StrictPhp\TypeChecker\TypeChecker\CallableTypeChecker::validate
     *
     * @dataProvider  mixedDataTypesToValidate
     *
     * @param string  $value
     * @param boolean $expected
     */
    public function testIfDataTypeIsValid($value, $expected)
    {
        $this->assertSame($expected, $this->callableCheck->validate($value, null));
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\CallableTypeChecker::simulateFailure
     */
    public function testSimulateFailure()
    {
        $this->assertAttributeEmpty('failingCallback', $this->callableCheck);
        $this->callableCheck->simulateFailure(function () {}, null);
        $this->assertAttributeNotEmpty('failingCallback', $this->callableCheck);
        $this->assertAttributeInternalType('callable', 'failingCallback', $this->callableCheck);
    }

    /**
     * @return mixed[][] - mixed type
     *                   - boolean expected
     *
     */
    public function mixedDataTypesToValidate()
    {
        return [
            [function () {},  true],
            [[],              false],
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
            ['callable', true],
            ['array',    false],
            ['string',   false],
            ['object',   false],
            ['boolean',  false],
            ['bool',     false],
            ['integer',  false],
            ['int',      false],
            ['null',     false],
            ['mixed',    false],
        ];
    }
}
