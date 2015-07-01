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
use ReflectionClass;
use ReflectionProperty;
use StrictPhp\TypeFinder\PropertyTypeFinder;
use StrictPhpTestAsset\ClassWithGenericNonTypedProperty;
use StrictPhpTestAsset\ClassWithGenericStringTypedProperty;
use StrictPhpTestAsset\ClassWithImportedHintClasses;
use StrictPhpTestAsset\ClassWithSameTypedProperty;
use StrictPhpTestAsset\ClassWithSelfTypedProperty;
use StrictPhpTestAsset\ClassWithStaticTypedProperty;

/**
 * Tests for {@see \StrictPhp\TypeFinder\PropertyTypeFinder}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\TypeFinder\PropertyTypeFinder
 */
class PropertyTypeFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidReflectionPropertyReturnAEmptyArray()
    {
        $this->assertEmpty(
            (new PropertyTypeFinder())
                ->__invoke(new ReflectionProperty(ClassWithGenericNonTypedProperty::class, 'property'), __CLASS__)
        );
    }

    /**
     * @dataProvider mixedAnnotationTypes
     *
     * @param string                  $annotation with annotation
     * @param string                  $contextClass
     * @param array                   $expected
     * @param ReflectionProperty|null $reflectionProperty
     */
    public function testValidReflectionPropertyReturnAEmptyArray(
        $annotation,
        $contextClass,
        array $expected,
        ReflectionProperty $reflectionProperty = null
    ) {
        if (! $reflectionProperty) {
            /** @var \ReflectionProperty|\PHPUnit_Framework_MockObject_MockObject $reflectionProperty */
            $reflectionProperty = $this->getMockBuilder(ReflectionProperty::class)
                ->setMethods(['getDocComment', 'getDeclaringClass'])
                ->disableOriginalConstructor()
                ->getMock();

            $reflectionProperty
                ->expects($this->any())
                ->method('getDeclaringClass')
                ->will($this->returnValue(new ReflectionClass(ClassWithGenericStringTypedProperty::class)));

            $reflectionProperty
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
                (new PropertyTypeFinder())
                    ->__invoke($reflectionProperty, $contextClass)
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
            ['/** @var */', __CLASS__, []],
            ['/** @var string */', __CLASS__, ['string']],
            ['/** @var integer */', __CLASS__, ['int']],
            ['/** @var int */', __CLASS__, ['int']],
            ['/** @var bool */', __CLASS__, ['bool']],
            ['/** @var boolean */', __CLASS__, ['bool']],
            ['/** @var array */', __CLASS__, ['array']],
            ['/** @var string[] */', __CLASS__, ['string[]']],
            ['/** @var null */', __CLASS__, ['null']],
            ['/** @var StdClass */', __CLASS__, ['\StrictPhpTestAsset\StdClass']],
            ['/** @var \StdClass */', __CLASS__, ['\StdClass']],
            ['/** @var \StdClass[] */', __CLASS__, ['\StdClass[]']],
            ['/** @var \StdClass|null|array */', __CLASS__, ['\StdClass', 'null', 'array']],
            ['/** @var \StdClass|AnotherClass */', __CLASS__, ['\StdClass', '\StrictPhpTestAsset\AnotherClass']],
            ['/** @var \My\Collection|\Some\Thing[] */', __CLASS__, ['\My\Collection', '\Some\Thing[]']],
            ['/** @var mixed */', __CLASS__, ['mixed']],
            [
                '/** @var self */',
                ClassWithSelfTypedProperty::class,
                ['\\' . ClassWithSelfTypedProperty::class],
                new ReflectionProperty(ClassWithSelfTypedProperty::class, 'property'),
            ],
            [
                '/** @var static */',
                ClassWithStaticTypedProperty::class,
                ['\\' . ClassWithStaticTypedProperty::class],
                new ReflectionProperty(ClassWithStaticTypedProperty::class, 'property'),
            ],
            [
                '/** @var \\' . ClassWithGenericStringTypedProperty::class . ' */',
                ClassWithSameTypedProperty::class,
                ['\\' . ClassWithSameTypedProperty::class],
                new ReflectionProperty(ClassWithSameTypedProperty::class, 'property'),
            ],
            'property with imported class hint' => [
                '',
                ClassWithImportedHintClasses::class,
                [
                    '\\Some\Imported\ClassName',
                    '\\Some\Imported\NamespaceName\AnotherClassName',
                ],
                new ReflectionProperty(ClassWithImportedHintClasses::class, 'property'),
            ],
        ];
    }
}
