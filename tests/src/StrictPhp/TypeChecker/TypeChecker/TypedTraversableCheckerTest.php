<?php

namespace StrictPhp\TypeChecker\TypeChecker;

use stdClass;

/**
 * Tests for {@see \StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 */
class TypedTraversableCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TypedTraversableChecker
     */
    private $typedCheck;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->typedCheck = new TypedTraversableChecker(
            ...[
                new ArrayTypeChecker(),
                new IntegerTypeChecker(),
            ]
        );
    }

    /**
     * @covers       \StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker::canApplyToType
     *
     * @dataProvider mixedDataTypes
     *
     * @param string  $type
     * @param boolean $expected
     */
    public function testTypeCanBeApplied($type, $expected)
    {
        $this->assertSame($expected, $this->typedCheck->canApplyToType($type));
    }

    /**
     * @covers        \StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker::validate
     * @covers        \StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker::getCheckersValidForType
     *
     * @dataProvider  mixedDataTypesToValidate
     *
     * @param string  $value
     * @param string  $type
     * @param boolean $expected
     */
    public function testIfDataTypeIsValid($value, $type, $expected)
    {
        $this->assertSame($expected, $this->typedCheck->validate($value, $type));
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker::simulateFailure
     * @covers \StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker::getCheckersApplicableToType
     */
    public function testSimulateFailureDoesNothingWhenPassAString()
    {
        $this->typedCheck->simulateFailure([], StdClass::class);
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
            [['Marco Pivetta'], 'string[]',      true],
            [[1, 2, 4],         'integer[]',     true],
            [[1, 2, 4],         'int[]',         true],
            ['4',               'array[]',       false],
            [new StdClass,      StdClass::class, false],
            [123,               'integer',       false],
            [0x12,              'int',           false],
            ['Marco Pivetta',   'string',        false],
            [[],                'array',         false],
            [true,              'boolean',       false],
            [null,              'null',          false],
        ];
    }

    /**
     * @return mixed[][] - string with type of data
     *                   - expected output
     */
    public function mixedDataTypes()
    {
        return [
            ['array[]',       true],
            ['integer[]',     true],
            [StdClass::class, false],
            ['integer',       false],
            ['int',           false],
            ['object',        false],
            ['string',        false],
            ['boolean',       false],
            ['bool',          false],
            ['null',          false],
            ['mixed',         false],
        ];
    }
}
