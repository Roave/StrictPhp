<?php

namespace StrictPhpTest\TypeFinder;

use phpDocumentor\Reflection\Type;
use ReflectionClass;
use ReflectionProperty;
use StrictPhp\TypeFinder\PropertyTypeFinder;
use StrictPhpTestAsset\ClassWithGenericNonTypedProperty;
use StrictPhpTestAsset\ClassWithGenericStringTypedProperty;

/**
 * Tests for {@see \StrictPhp\TypeFinder\PropertyTypeFinder}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 */
class PropertyTypeFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \StrictPhp\TypeFinder\PropertyTypeFinder::__invoke
     */
    public function testInvalidReflectionPropertyReturnAEmptyArray()
    {
        $this->assertEmpty(
            (new PropertyTypeFinder())
                ->__invoke(new ReflectionProperty(ClassWithGenericNonTypedProperty::class, 'property'), __CLASS__)
        );
    }

    /**
     * @covers       \StrictPhp\TypeFinder\PropertyTypeFinder::__invoke
     *
     * @dataProvider mixedAnnotationTypes
     *
     * @param string $annotation with annotation
     * @param string $contextClass
     * @param array  $expected
     */
    public function testValidReflectionPropertyReturnAEmptyArray($annotation, $contextClass, array $expected)
    {
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
                ClassWithGenericStringTypedProperty::class,
                ['\\' . ClassWithGenericStringTypedProperty::class]
            ],
            [
                '/** @var static */',
                ClassWithGenericStringTypedProperty::class,
                ['\\' . ClassWithGenericStringTypedProperty::class]
            ],
            [
                '/** @var \\' . ClassWithGenericStringTypedProperty::class . ' */',
                ClassWithGenericStringTypedProperty::class,
                ['\\' . ClassWithGenericStringTypedProperty::class]
            ],
        ];
    }
}
