<?php

namespace StrictPhp\TypeChecker\TypeChecker;

/**
 * Tests for {@see \StrictPhp\TypeChecker\TypeChecker\GenericObjectTypeChecker}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 */
class GenericObjectTypeCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenericObjectTypeChecker
     */
    private $objectCheck;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->objectCheck = new GenericObjectTypeChecker();
    }

    /**
     * @covers       \StrictPhp\TypeChecker\TypeChecker\GenericObjectTypeChecker::canApplyToType
     *
     * @dataProvider mixedDataTypes
     *
     * @param string  $type
     * @param boolean $expected
     */
    public function testTypeCanBeApplied($type, $expected)
    {
        $this->assertSame($expected, $this->objectCheck->canApplyToType($type));
    }

    /**
     * @covers        \StrictPhp\TypeChecker\TypeChecker\GenericObjectTypeChecker::validate
     *
     * @dataProvider  mixedDataTypesToValidate
     *
     * @param string  $value
     * @param boolean $expected
     */
    public function testIfDataTypeIsValid($value, $expected)
    {
        $this->assertSame($expected, $this->objectCheck->validate($value, null));
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\GenericObjectTypeChecker::simulateFailure
     */
    public function testSimulateFailureRaisesExceptionWhenNotPassAString()
    {
        $this->setExpectedException(\ErrorException::class);
        $this->objectCheck->simulateFailure([], null);
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\GenericObjectTypeChecker::simulateFailure
     */
    public function testSimulateFailureDoesNothingWhenPassAString()
    {
        $this->objectCheck->simulateFailure(new \StdClass, null);
    }

    /**
     * @return mixed[][] - mixed type
     *                   - boolean expected
     *
     */
    public function mixedDataTypesToValidate()
    {
        return [
            [new \StdClass,   true],
            ['Marco Pivetta', false],
            [[],              false],
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
            ['object',  true],
            ['string',  false],
            ['array',   false],
            ['boolean', false],
            ['bool',    false],
            ['integer', false],
            ['int',     false],
            ['null',    false],
            ['mixed',   false],
        ];
    }
}
