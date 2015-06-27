<?php

namespace StrictPhpTest\TypeChecker;

use phpDocumentor\Reflection\Types\Boolean;
use StrictPhp\TypeChecker\ApplyTypeChecks;
use StrictPhp\TypeChecker\TypeCheckerInterface;

/**
 * Tests for {@see \StrictPhp\TypeChecker\ApplyTypeChecks}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\TypeChecker\ApplyTypeChecks
 */
class ApplyTypeChecksTest extends \PHPUnit_Framework_TestCase
{
    public function testApplyCheckerProperly()
    {
        $booleanType = new Boolean();
        $typeChecker = $this->getMock(TypeCheckerInterface::class);
        $typeChecker->expects($this->once())
            ->method('canApplyToType')
            ->with($booleanType)
            ->will($this->returnValue(true));

        $typeChecker->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(true));

        $typeChecker->expects($this->once())
            ->method('simulateFailure')
            ->will($this->returnValue(true));

        $applyChecks = new ApplyTypeChecks($typeChecker);
        $applyChecks->__invoke([$booleanType], []);
    }

    public function testWillApplyNonFittingCheckersIfAnyAreFound()
    {
        $booleanType = new Boolean();
        $typeChecker = $this->getMock(TypeCheckerInterface::class);
        $typeChecker->expects($this->once())
            ->method('canApplyToType')
            ->with($booleanType)
            ->will($this->returnValue(true));

        $typeChecker->expects($this->once())
            ->method('validate')
            ->with([], $booleanType)
            ->will($this->returnValue(false));

        $typeChecker->expects($this->once())
            ->method('simulateFailure')
            ->with([], $booleanType)
            ->will($this->returnValue(true));

        $applyChecks = new ApplyTypeChecks($typeChecker);
        $applyChecks->__invoke([$booleanType], []);
    }
}
