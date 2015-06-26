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
        $this->assertSame(
            [],
            (new PropertyTypeFinder())->__invoke(new ReflectionProperty(
                ClassWithGenericNonTypedProperty::class,
                'property'
            ))
        );
    }

    /**
     * @covers       \StrictPhp\TypeFinder\PropertyTypeFinder::__invoke
     *
     * @dataProvider mixedAnnotationTypes
     *
     * @param string $annotation with annotation
     * @param array  $expected
     */
    public function testValidReflectionPropertyReturnAEmptyArray($annotation, array $expected)
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
                (new PropertyTypeFinder())->__invoke($reflectionProperty)
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
            ['/** */', []],
            ['/** @var */', []],
            ['/** @var string */', ['string']],
            ['/** @var integer */', ['int']],
            ['/** @var int */', ['int']],
            ['/** @var bool */', ['bool']],
            ['/** @var boolean */', ['bool']],
            ['/** @var array */', ['array']],
            ['/** @var string[] */', ['string[]']],
            ['/** @var null */', ['null']],
            ['/** @var StdClass */', ['\StdClass']],
            ['/** @var \StdClass */', ['\StdClass']],
            ['/** @var \StdClass[] */', ['\StdClass[]']],
            ['/** @var \StdClass|null|array */', ['\StdClass', 'null', 'array']],
            ['/** @var \StdClass|AnotherClass */', ['\StdClass', '\AnotherClass']],
            ['/** @var \My\Collection|\Some\Thing[] */', ['\My\Collection', '\Some\Thing[]']],
            ['/** @var mixed */', ['mixed']],
        ];
    }
}
