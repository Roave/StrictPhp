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
use StrictPhp\TypeChecker\TypeChecker\ArrayTypeChecker;

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
     * @param Type    $type
     * @param boolean $expected
     */
    public function testTypeCanBeApplied(Type $type, $expected)
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
        $this->assertSame($expected, $this->arrayCheck->validate($value, new Array_()));
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\ArrayTypeChecker::simulateFailure
     */
    public function testSimulateFailure()
    {
        $this->assertAttributeEmpty('failingCallback', $this->arrayCheck);
        $this->arrayCheck->simulateFailure([], new Array_());
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
            [new Array_(),                             true],
            [new String_(),                            false],
            [new Object_(),                            false],
            [new Object_(new Fqsen('\\' . __CLASS__)), false],
            [new Boolean(),                            false],
            [new Integer(),                            false],
            [new Null_(),                              false],
            [new Mixed(),                              false],
        ];
    }
}
