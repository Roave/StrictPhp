<?php

namespace StrictPhpTest\Reflection;

use ReflectionClass;
use StrictPhp\Reflection\AllProperties;
use StrictPhpTestAsset\ClassWithIncorrectlyInitializedParentClassProperties;
use StrictPhpTestAsset\ClassWithIncorrectlyInitializingConstructor;
use StrictPhpTestAsset\ParentClassWithInitializingConstructor;

/**
 * Tests for {@see \StrictPhp\Reflection\AllProperties}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\Reflection\AllProperties
 */
class AllPropertiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider propertiesCount
     *
     * @param string $className
     * @param int    $expectedCount
     */
    public function testPropertiesCount($className, $expectedCount)
    {
        $this->assertCount($expectedCount, (new AllProperties())->__invoke(new ReflectionClass($className)));
    }

    /**
     * @return int[][]|string[][]
     */
    public function propertiesCount()
    {
        return [
            [ClassWithIncorrectlyInitializingConstructor::class, 1],
            [ParentClassWithInitializingConstructor::class, 1],
            [ClassWithIncorrectlyInitializedParentClassProperties::class, 2],
        ];
    }
}
