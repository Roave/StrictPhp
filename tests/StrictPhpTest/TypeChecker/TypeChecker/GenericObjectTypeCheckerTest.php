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
use StrictPhp\TypeChecker\TypeChecker\GenericObjectTypeChecker;

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
     * @param Type    $type
     * @param boolean $expected
     */
    public function testTypeCanBeApplied(Type $type, $expected)
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
        $this->assertSame($expected, $this->objectCheck->validate($value, new Object_()));
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\GenericObjectTypeChecker::simulateFailure
     */
    public function testSimulateFailureRaisesExceptionWhenNotPassAString()
    {
        $this->setExpectedException(\ErrorException::class);
        $this->objectCheck->simulateFailure([], new Object_());
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\GenericObjectTypeChecker::simulateFailure
     */
    public function testSimulateFailureDoesNothingWhenPassAString()
    {
        $this->objectCheck->simulateFailure(new \StdClass, new Object_());

        // @TODO add assertion here
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
            [new Object_(),                            true],
            [new Object_(new Fqsen('\\' . __CLASS__)), false],
            [new String_(),                            false],
            [new Array_(),                             false],
            [new Boolean(),                            false],
            [new Integer(),                            false],
            [new Null_(),                              false],
            [new Mixed(),                              false],
        ];
    }
}
