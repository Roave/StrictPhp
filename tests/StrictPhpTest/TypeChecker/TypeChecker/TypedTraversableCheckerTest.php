<?php

namespace StrictPhpTest\TypeChecker\TypeChecker;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use stdClass;
use StrictPhp\TypeChecker\TypeChecker\ArrayTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker;

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
     * @param Type    $type
     * @param boolean $expected
     */
    public function testTypeCanBeApplied(Type $type, $expected)
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
     * @param Type    $type
     * @param boolean $expected
     */
    public function testIfDataTypeIsValid($value, Type $type, $expected)
    {
        $this->assertSame($expected, $this->typedCheck->validate($value, $type));
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker::simulateFailure
     * @covers \StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker::getCheckersApplicableToType
     */
    public function testSimulateFailureDoesNothingWhenPassAString()
    {
        $this->typedCheck->simulateFailure([], new Array_());
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
            [['Marco Pivetta'], new Array_(new String_()),                                  true],
            [[1, 2, 4],         new Array_(new Integer()),                                  true],
            ['4',               new Array_(new Array_()),                                   false],
            [[['4']],           new Array_(new Array_()),                                   true],
            [new StdClass,      new Array_(new Object_(new Fqsen('\\' . StdClass::class))), false],
            [123,               new Integer(),                                              false],
            [0x12,              new Integer(),                                              false],
            ['Marco Pivetta',   new String_(),                                              false],
            [[],                new Array_(),                                               true],
            [true,              new Boolean(),                                              false],
            [null,              new Null_(),                                                false],
        ];
    }

    /**
     * @return mixed[][] - string with type of data
     *                   - expected output
     */
    public function mixedDataTypes()
    {
        return [
            [new Array_(new Array_()),                                   true],
            [new Array_(new Integer()),                                  true],
            [new Array_(new Object_(new Fqsen('\\' . StdClass::class))), false],
            [new Integer(),                                              false],
            [new Object_(),                                              false],
            [new String_(),                                              false],
            [new Boolean(),                                              false],
            [new Null_(),                                                false],
            [new Mixed(),                                                false],
        ];
    }
}
