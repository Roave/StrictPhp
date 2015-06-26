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
use StrictPhp\TypeChecker\TypeChecker\MixedTypeChecker;

/**
 * Tests for {@see \StrictPhp\TypeChecker\TypeChecker\MixedTypeChecker}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\TypeChecker\TypeChecker\MixedTypeChecker
 */
class MixedTypeCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MixedTypeChecker
     */
    private $mixedChecker;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->mixedChecker = new MixedTypeChecker();
    }

    /**
     * @dataProvider mixedDataTypes
     *
     * @param Type    $type
     * @param boolean $expected
     */
    public function testTypeCanBeApplied(Type $type, $expected)
    {
        $this->assertSame($expected, $this->mixedChecker->canApplyToType($type));
    }

    /**
     * @dataProvider  mixedDataTypesToValidate
     *
     * @param string  $value
     * @param boolean $expected
     */
    public function testIfDataTypeIsValid($value, $expected)
    {
        $this->assertSame($expected, $this->mixedChecker->validate($value, new Mixed()));
    }

    public function testRejectsInvalidTypeValidation()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        $this->mixedChecker->validate('foo', new String_());
    }

    public function testSimulateSuccess()
    {
        $this->mixedChecker->simulateFailure([], new Mixed());

        // @TODO assertion?
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
            [new \StdClass,   true],
            [true,            true],
            [null,            true],
            [123,             true],
            [1e-3,            true],
            [0x12,            true],
            ['Marco Pivetta', true],
        ];
    }

    /**
     * @return mixed[][] - string with type of data
     *                   - expected output
     */
    public function mixedDataTypes()
    {
        return [
            [new Mixed(),                              true],
            [new Array_(),                             false],
            [new String_(),                            false],
            [new Object_(),                            false],
            [new Object_(new Fqsen('\\' . __CLASS__)), false],
            [new Boolean(),                            false],
            [new Integer(),                            false],
            [new Null_(),                              false],
        ];
    }
}
