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

namespace StrictPhpTest;

use Go\Core\AspectContainer;
use StrictPhp\Aspect\PostConstructAspect;
use StrictPhp\Aspect\PostPublicMethodAspect;
use StrictPhp\Aspect\PrePublicMethodAspect;
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
     *
     * @runInSeparateProcess
     */
    public function testRegisteredDefaultAspects()
    {
        $strictPhp = StrictPhpKernel::bootstrap([
            'cacheDir' => realpath(__DIR__ . '/..') . '/integration-tests-go-cache/',
            'includePaths' => [
                __DIR__,
            ],
        ]);

        $container = $strictPhp->getContainer();
        $this->assertInstanceOf(PropertyWriteAspect::class, $container->getAspect(PropertyWriteAspect::class));
        $this->assertInstanceOf(PostConstructAspect::class, $container->getAspect(PostConstructAspect::class));
    }

    /**
     * @covers \StrictPhp\StrictPhpKernel::configureAop
     *
     * @runInSeparateProcess
     */
    public function testWillAllowDisablingAllAspects()
    {
        $strictPhp = StrictPhpKernel::bootstrap(
            [
                'cacheDir' => realpath(__DIR__ . '/..') . '/integration-tests-go-cache/',
                'includePaths' => [__DIR__],
            ],
            []
        );

        $container = $strictPhp->getContainer();

        $this->assertInstanceOf(AspectContainer::class, $container);

        $nonRegisteredAspects = [
            PostConstructAspect::class,
            PrePublicMethodAspect::class,
            PostPublicMethodAspect::class,
            PropertyWriteAspect::class,
        ];

        foreach ($nonRegisteredAspects as $aspectName) {
            try {
                $container->getAspect($aspectName);

                $this->fail('No exception was thrown');
            } catch (\OutOfBoundsException $exception) {
                // empty catch, on purpose
            }
        }
    }
}
