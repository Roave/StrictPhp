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
use StrictPhp\TypeChecker\TypeChecker\StringTypeChecker;

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
     * @param Type    $type
     * @param boolean $expected
     */
    public function testTypeCanBeApplied(Type $type, $expected)
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
        $this->assertSame($expected, $this->stringCheck->validate($value, new String_()));
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\StringTypeChecker::simulateFailure
     */
    public function testSimulateFailureRaisesExceptionWhenNotPassAString()
    {
        $this->setExpectedException(\ErrorException::class);
        $this->stringCheck->simulateFailure([], new String_());
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\StringTypeChecker::simulateFailure
     */
    public function testSimulateFailureDoesNothingWhenPassAString()
    {
        $this->stringCheck->simulateFailure('Marco Pivetta', new String_());

        // @TODO add assertion
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
            [new String_(),                             true],
            [new Array_(),                              false],
            [new Object_(),                             false],
            [new Object_(new Fqsen('\\' . __CLASS__)),  false],
            [new Boolean(),                             false],
            [new Integer(),                             false],
            [new Null_(),                               false],
            [new Mixed(),                               false],
        ];
    }
}

