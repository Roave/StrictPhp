<?php

namespace StrictPhpTest;

use StrictPhp\Aspect\PostConstructAspect;
use StrictPhp\Aspect\PropertyWriteAspect;
use StrictPhp\StrictPhpKernel;

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
            'includePaths' => [
                __DIR__,
            ],
        ]);

        $container = $strictPhp->getContainer();
        $this->assertInstanceOf(PropertyWriteAspect::class, $container->getAspect(PropertyWriteAspect::class));
        $this->assertInstanceOf(PostConstructAspect::class, $container->getAspect(PostConstructAspect::class));
    }
}
