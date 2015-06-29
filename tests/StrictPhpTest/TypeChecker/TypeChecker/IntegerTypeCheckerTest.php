<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace StrictPhpTest\TypeChecker\TypeChecker;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker;

/**
 * Tests for {@see \StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 */
class IntegerTypeCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IntegerTypeChecker
     */
    private $integerCheck;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->integerCheck = new IntegerTypeChecker();
    }

    /**
     * @covers       \StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker::canApplyToType
     *
     * @dataProvider mixedDataTypes
     *
     * @param Type    $type
     * @param boolean $expected
     */
    public function testTypeCanBeApplied(Type $type, $expected)
    {
        $this->assertSame($expected, $this->integerCheck->canApplyToType($type));
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
        $this->assertSame($expected, $this->integerCheck->validate($value, new Integer()));
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker::simulateFailure
     */
    public function testSimulateFailureRaisesExceptionWhenNotPassAString()
    {
        $this->setExpectedException(\ErrorException::class);
        $this->integerCheck->simulateFailure([], new Integer());
    }

    /**
     * @covers \StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker::simulateFailure
     */
    public function testSimulateFailureDoesNothingWhenPassAString()
    {
        $this->integerCheck->simulateFailure(10, new Integer());
    }

    /**
     * @return mixed[][] - mixed type
     *                   - boolean expected
     *
     */
    public function mixedDataTypesToValidate()
    {
        return [
            [123,             true],
            [0x12,            true],
            [new \StdClass,   false],
            ['Marco Pivetta', false],
            [[],              false],
            [true,            false],
            [null,            false],
        ];
    }

    /**
     * @return mixed[][] - string with type of data
     *                   - expected output
     */
    public function mixedDataTypes()
    {
        return [
            [new Integer(), true],
            [new Object_(), false],
            [new String_(), false],
            [new Array_(),  false],
            [new Boolean(), false],
            [new Null_(),    false],
            [new Mixed(),   false],
        ];
    }
}
