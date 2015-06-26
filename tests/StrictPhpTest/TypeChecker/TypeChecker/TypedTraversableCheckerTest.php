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
use StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\StringTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker;

/**
 * Tests for {@see \StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker
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
        $this->typedCheck = new TypedTraversableChecker(...[
            new ArrayTypeChecker(),
            new IntegerTypeChecker(),
            new StringTypeChecker(),
            new ObjectTypeChecker(),
        ]);
    }

    /**
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
     * @dataProvider  mixedDataTypesToValidate
     *
     * @param string  $value
     * @param Type    $type
     * @param boolean $expected
     */
    public function testFailureWithData($value, Type $type, $expected)
    {
        if (! $expected) {
            // catching the exception raised by PHPUnit by converting a fatal into an exception (in the error handler)
            $this->setExpectedException(\PHPUnit_Framework_Error::class);
        }

        $this->typedCheck->simulateFailure(
            [new stdClass()],
            new Array_(new Object_(new Fqsen('\\' . stdClass::class)))
        );

        // @TODO assertion?
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
            [['foo'],           new Array_(),                                               true],
            [['Marco Pivetta'], new Array_(new String_()),                                  true],
            [[1, 2, 4],         new Array_(new Integer()),                                  true],
            ['4',               new Array_(new Array_()),                                   false],
            [[['4']],           new Array_(new Array_()),                                   true],
            [[[['4']]],         new Array_(new Array_(new Array_())),                       true],
            [[['4']],           new Array_(new Array_(new Array_())),                       false],
            [new StdClass,      new Array_(new Object_(new Fqsen('\\' . StdClass::class))), false],
            [[new StdClass],    new Array_(new Object_(new Fqsen('\\' . StdClass::class))), true],
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
            [new Array_(new Object_(new Fqsen('\\' . StdClass::class))), true],
            [new Array_(new Mixed()),                                    false],
            [new Integer(),                                              false],
            [new Object_(),                                              false],
            [new String_(),                                              false],
            [new Boolean(),                                              false],
            [new Null_(),                                                false],
            [new Mixed(),                                                false],
        ];
    }
}
