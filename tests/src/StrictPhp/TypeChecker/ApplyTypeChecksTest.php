<?php

namespace StrictPhp\TypeChecker;

/**
 * Tests for {@see \StrictPhp\TypeChecker\ApplyTypeChecks}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 */
class ApplyTypeChecksTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \StrictPhp\TypeChecker\ApplyTypeChecks::__construct
     * @covers \StrictPhp\TypeChecker\ApplyTypeChecks::__invoke
     */
    public function testApplyCheckerProperly()
    {
        $typeChecker = $this->getMock(TypeCheckerInterface::class);
        $typeChecker->expects($this->once())
            ->method('canApplyToType')
            ->with('boolean')
            ->will($this->returnValue(true));

        $typeChecker->expects($this->once())
            ->method('validate')
            ->will($this->returnValue(true));

        $typeChecker->expects($this->once())
            ->method('simulateFailure')
            ->will($this->returnValue(true));

        $applyChecks = new ApplyTypeChecks($typeChecker);
        $applyChecks->__invoke(['boolean'], []);
    }
}
