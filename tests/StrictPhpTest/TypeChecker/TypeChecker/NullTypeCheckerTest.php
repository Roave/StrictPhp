<?php

namespace StrictPhpTest\TypeChecker\TypeChecker;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use StrictPhp\TypeChecker\TypeChecker\NullTypeChecker;

/**
 * Tests for {@see \StrictPhp\TypeChecker\TypeChecker\NullTypeChecker}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\TypeChecker\TypeChecker\NullTypeChecker
 */
class NullTypeCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NullTypeChecker
     */
    private $nullCheck;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->nullCheck = new NullTypeChecker();
    }

    public function testRejectsNotNullType()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        $this->nullCheck->validate(null, new Integer());
    }

    /**
     * @dataProvider mixedDataTypes
     *
     * @param Type    $type
     * @param boolean $expected
     */
    public function testTypeCanBeApplied(Type $type, $expected)
    {
        $this->assertSame($expected, $this->nullCheck->canApplyToType($type));
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
        $this->assertSame($expected, $this->nullCheck->validate($value, new Null_()));
    }

    public function testSimulateFailureRaisesExceptionWhenNotPassAString()
    {
        $this->setExpectedException(\ErrorException::class);

        $this->nullCheck->simulateFailure([], new Null_());
    }

    public function testSimulateFailureDoesNothingWhenPassAString()
    {
        $this->nullCheck->simulateFailure(null, new Null_());
    }

    /**
     * @return mixed[][] - mixed type
     *                   - boolean expected
     *
     */
    public function mixedDataTypesToValidate()
    {
        return [
            [123,             false],
            [0x12,            false],
            [new \StdClass,   false],
            ['Marco Pivetta', false],
            [[],              false],
            [true,            false],
            [null,            true],
        ];
    }

    /**
     * @return mixed[][] - string with type of data
     *                   - expected output
     */
    public function mixedDataTypes()
    {
        return [
            [new Null_(),   true],
            [new Integer(), false],
            [new Object_(), false],
            [new String_(), false],
            [new Array_(),  false],
            [new Boolean(), false],
            [new Mixed(),   false],
        ];
    }
}
