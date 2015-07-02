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

namespace StrictPhpTest\TypeFinder;

use phpDocumentor\Reflection\Type;
use ReflectionMethod;
use StrictPhp\TypeFinder\ReturnTypeFinder;
use StrictPhpTestAsset\ClassWithReturnTypeMethod;

/**
 * Tests for {@see \StrictPhp\TypeFinder\ReturnTypeFinder}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\TypeFinder\ReturnTypeFinder
 */
class ReturnTypeFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testReflectionMethodWithoutReturnTagShouldReturnEmptyValue()
    {
        $this->assertEmpty(
            (new ReturnTypeFinder())
                ->__invoke(new ReflectionMethod(ClassWithReturnTypeMethod::class, 'noReturnTag'), __CLASS__)
        );
    }

    /**
     * @dataProvider mixedAnnotationTypes
     *
     * @param string                $annotation with annotation
     * @param string                $contextClass
     * @param array                 $expected
     * @param ReflectionMethod|null $reflectionMethod
     */
    public function testValidReflectionMethodReturnExpectedValues(
        $annotation,
        $contextClass,
        array $expected,
        ReflectionMethod $reflectionMethod = null
    ) {
        if (! $reflectionMethod) {
            /** @var \ReflectionMethod|\PHPUnit_Framework_MockObject_MockObject $reflectionMethod */
            $reflectionMethod = $this->getMockBuilder(ReflectionMethod::class)
                ->setMethods(['getDocComment', 'getDeclaringClass'])
                ->disableOriginalConstructor()
                ->getMock();

            $reflectionMethod
                ->expects($this->any())
                ->method('getDeclaringClass')
                ->will($this->returnValue(new \ReflectionClass(ClassWithReturnTypeMethod::class)));

            $reflectionMethod
                ->expects($this->once())
                ->method('getDocComment')
                ->will($this->returnValue($annotation));
        }

        $this->assertSame(
            $expected,
            array_map(
                function (Type $type) {
                    return (string) $type;
                },
                (new ReturnTypeFinder())
                    ->__invoke($reflectionMethod, $contextClass)
            )
        );
    }

    /**
     * @return mixed[][] - string with annotation declaration
     *                   - array with result expected
     */
    public function mixedAnnotationTypes()
    {
        return [
            ['/** */', __CLASS__, []],
            ['/** @return */', __CLASS__, []],
            ['/** @return string */', __CLASS__, ['string']],
            ['/** @return integer */', __CLASS__, ['int']],
            ['/** @return int */', __CLASS__, ['int']],
            ['/** @return bool */', __CLASS__, ['bool']],
            ['/** @return boolean */', __CLASS__, ['bool']],
            ['/** @return array */', __CLASS__, ['array']],
            ['/** @return string[] */', __CLASS__, ['string[]']],
            ['/** @return null */', __CLASS__, ['null']],
            ['/** @return StdClass */', __CLASS__, ['\StrictPhpTestAsset\StdClass']],
            ['/** @return \StdClass */', __CLASS__, ['\StdClass']],
            ['/** @return \StdClass[] */', __CLASS__, ['\StdClass[]']],
            ['/** @return \StdClass|null|array */', __CLASS__, ['\StdClass', 'null', 'array']],
            ['/** @return \StdClass|AnotherClass */', __CLASS__, ['\StdClass', '\StrictPhpTestAsset\AnotherClass']],
            ['/** @return \My\Collection|\Some\Thing[] */', __CLASS__, ['\My\Collection', '\Some\Thing[]']],
            ['/** @return mixed */', __CLASS__, ['mixed']],
            [
                '/** @return self */',
                ClassWithReturnTypeMethod::class,
                ['\\' . ClassWithReturnTypeMethod::class],
                new ReflectionMethod(ClassWithReturnTypeMethod::class, 'expectSelf'),
            ],
            [
                '/** @return static */',
                ClassWithReturnTypeMethod::class,
                ['\\' . ClassWithReturnTypeMethod::class],
                new ReflectionMethod(ClassWithReturnTypeMethod::class, 'expectStatic'),
            ],
            [
                '/** @return \\' . ClassWithReturnTypeMethod::class . ' */',
                ClassWithReturnTypeMethod::class,
                ['\\' . ClassWithReturnTypeMethod::class],
                new ReflectionMethod(ClassWithReturnTypeMethod::class, 'expectThis'),
            ],
        ];
    }
}
