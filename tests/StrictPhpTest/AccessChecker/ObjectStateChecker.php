<?php

namespace StrictPhpTest\Aspect;

use phpDocumentor\Reflection\Types\Object_;
use ReflectionProperty;
use StrictPhp\AccessChecker\ObjectStateChecker;
use StrictPhpTestAsset\ClassWithIncorrectlyInitializedParentClassProperties;
use StrictPhpTestAsset\ParentClassWithInitializingConstructor;

/**
 * Tests for {@see \StrictPhp\AccessChecker\ObjectStateChecker}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\AccessChecker\ObjectStateChecker
 */
class ObjectStateCheckerTest extends \PHPUnit_Framework_TestCase
{
    public function testRejectsInvalidObject()
    {
        $checker = new ObjectStateChecker('count', 'count');

        $this->setExpectedException(\InvalidArgumentException::class);

        $checker->__invoke('not an object', __CLASS__);
    }

    public function testAppliesTypeChecksToAllObjectProperties()
    {
        /* @var $applyTypeChecks callable|\PHPUnit_Framework_MockObject_MockObject */
        $applyTypeChecks = $this->getMock('stdClass', ['__invoke']);
        /* @var $findTypes callable|\PHPUnit_Framework_MockObject_MockObject */
        $findTypes       = $this->getMock('stdClass', ['__invoke']);
        $objectType      = new Object_();
        $checker         = new ObjectStateChecker($applyTypeChecks, $findTypes);

        $applyTypeChecks->expects($this->exactly(2))->method('__invoke')->with(
            [$objectType],
            $this->logicalOr(null, ['the child class array'])
        );
        $findTypes
            ->expects($this->exactly(2))
            ->method('__invoke')
            ->with(
                $this->logicalOr(
                    $this->callback(function (ReflectionProperty $property) {
                        return ClassWithIncorrectlyInitializedParentClassProperties::class === $property->getDeclaringClass()->getName();
                    }),
                    $this->callback(function (ReflectionProperty $property) {
                        return ParentClassWithInitializingConstructor::class === $property->getDeclaringClass()->getName();
                    })
                ),
                $this->logicalOr(
                    ClassWithIncorrectlyInitializedParentClassProperties::class,
                    ParentClassWithInitializingConstructor::class
                )
            )
            ->will($this->returnValue([$objectType]));

        $checker->__invoke(
            new ClassWithIncorrectlyInitializedParentClassProperties(),
            ClassWithIncorrectlyInitializedParentClassProperties::class
        );
    }
}
