<?php

namespace StrictPhpTest\TypeFinder;

use ReflectionProperty;
use StrictPhp\TypeFinder\PropertyTypeFinder;

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
        /** @var \ReflectionProperty|\PHPUnit_Framework_MockObject_MockObject $reflectionProperty */
        $reflectionProperty = $this->getMockBuilder(ReflectionProperty::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertSame([], (new PropertyTypeFinder())->__invoke($reflectionProperty));
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
            ->setMethods(['getDocComment'])
            ->disableOriginalConstructor()
            ->getMock();

        $reflectionProperty
            ->expects($this->once())
            ->method('getDocComment')
            ->will($this->returnValue($annotation));

        $this->assertSame($expected, (new PropertyTypeFinder())->__invoke($reflectionProperty));
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
            ['/** @var integer */', ['integer']],
            ['/** @var int */', ['int']],
            ['/** @var bool */', ['bool']],
            ['/** @var boolean */', ['boolean']],
            ['/** @var array */', ['array']],
            ['/** @var string[] */', ['string[]']],
            ['/** @var null */', ['null']],
            ['/** @var StdClass */', ['\StdClass']],
            ['/** @var \StdClass */', ['\StdClass']],
            ['/** @var \StdClass[] */', ['\StdClass[]']],
            ['/** @var \StdClass|null|array */', ['\StdClass', 'null', 'array']],
            ['/** @var \StdClass|AnotherClass */', ['\StdClass', '\AnotherClass']],
            ['/** @var mixed */', ['mixed']],
        ];
    }
}
