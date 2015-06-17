<?php

namespace StrictPhp;

use StrictPhp\Aspect\ImmutablePropertyCheck;
use StrictPhp\Aspect\PropertyWriteTypeCheck;

/**
 * Tests for {@see \StrictPhp\StrictPhpKernel}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 */
class StrictPhpKernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \StrictPhp\StrictPhpKernel::configureAop
     */
    public function testIfAspectsWasRegisteredProperly()
    {
        $strictPhp = StrictPhpKernel::getInstance();
        $strictPhp->init([
            'cacheDir' => realpath(__DIR__ . '/../..') . '/go-cache/',
        ]);

        $container = $strictPhp->getContainer();
        $this->assertInstanceOf(ImmutablePropertyCheck::class, $container->getAspect(ImmutablePropertyCheck::class));
        $this->assertInstanceOf(PropertyWriteTypeCheck::class, $container->getAspect(PropertyWriteTypeCheck::class));
    }
}
