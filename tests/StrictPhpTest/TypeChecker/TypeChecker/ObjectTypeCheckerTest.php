<?php

namespace StrictPhpTest\TypeChecker\TypeChecker;

use DateTime;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use StdClass;
use StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker;

/**
 * Tests for {@see \StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 */
class ObjectTypeCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectTypeChecker
     */
    private $objectTypeCheck;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->objectTypeCheck = new ObjectTypeChecker();
    }

    /**
     * @covers       \StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker::canApplyToType
     *
     * @dataProvider mixedDataTypes
     *
     * @param Type    $type
     * @param boolean $expected
     */
    public function testTypeCanBeApplied(Type $type, $expected)
    {
        $this->assertSame($expected, $this->objectTypeCheck->canApplyToType($type));
    }

    /**
     * @covers        \StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker::validate
     *
     * @dataProvider  mixedDataTypesToValidate
     *
     * @param string  $value
     * @param Type    $type
     * @param boolean $expected
     */
    public function testIfDataTypeIsValid($value, Type $type, $expected)
    {
        $this->assertSame($expected, $this->objectTypeCheck->validate($value, $type));
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker::simulateFailure
     */
    public function testSimulateFailureRaisesExceptionWhenPassingAnArray()
    {
        // catching the exception raised by PHPUnit by converting a fatal into an exception (in the error handler)
        $this->setExpectedException(\PHPUnit_Framework_Error::class);

        $this->objectTypeCheck->simulateFailure([], new Object_(new Fqsen('\\' . StdClass::class)));
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker::simulateFailure
     */
    public function testSimulateFailureRaisesExceptionWhenNotPassAString()
    {
        // catching the exception raised by PHPUnit by converting a fatal into an exception (in the error handler)
        $this->setExpectedException(\PHPUnit_Framework_Error::class);

        $this->objectTypeCheck->simulateFailure('Marco Pivetta', new Object_(new Fqsen('\\' . StdClass::class)));
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker::simulateFailure
     */
    public function testSimulateFailureDoesNothingWhenPassAString()
    {
        $this->objectTypeCheck->simulateFailure(new StdClass, new Object_(new Fqsen('\\' . StdClass::class)));
        $this->objectTypeCheck->simulateFailure(new DateTime, new Object_(new Fqsen('\\' . DateTime::class)));

        // @TODO add assertions here
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
            [new StdClass,    new Object_(new Fqsen('\\' . StdClass::class)), true],
            [new DateTime,    new Object_(new Fqsen('\\' . DateTime::class)), true],
            [123,             new Object_(new Fqsen('\\' . StdClass::class)), false],
            [0x12,            new Object_(new Fqsen('\\' . StdClass::class)), false],
            ['Marco Pivetta', new Object_(new Fqsen('\\' . StdClass::class)), false],
            [[],              new Object_(new Fqsen('\\' . StdClass::class)), false],
            [true,            new Object_(new Fqsen('\\' . StdClass::class)), false],
            [null,            new Object_(new Fqsen('\\' . StdClass::class)), false],
        ];
    }

    /**
     * @return mixed[][] - string with type of data
     *                   - expected output
     */
    public function mixedDataTypes()
    {
        return [
            [new Object_(new Fqsen('\\' . StdClass::class)), true],
            [new Object_(new Fqsen('\\UnknownStuff')),       true],
            [new Integer(),                                  false],
            [new Object_(),                                  false],
            [new String_(),                                  false],
            [new Array_(),                                   false],
            [new Boolean(),                                  false],
            [new Null_(),                                    false],
            [new Mixed(),                                    false],
        ];
    }
}
