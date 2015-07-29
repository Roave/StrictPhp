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
 *
 * @covers \StrictPhp\StrictPhpKernel
 *
 */
class StrictPhpKernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    private $baseConfig = [];

    /**
     * @var string[]
     */
    private $allExpectedAspects = [
        PostConstructAspect::class,
        PrePublicMethodAspect::class,
        PostPublicMethodAspect::class,
        PropertyWriteAspect::class,
    ];

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->baseConfig = [
            'cacheDir' => realpath(__DIR__ . '/..') . '/integration-tests-go-cache/',
            'includePaths' => [__DIR__],
        ];
    }

    /**
     * @runInSeparateProcess
     */
    public function testRegisteredDefaultAspects()
    {
        $container = $this->buildContainer();

        $this->assertInstanceOf(PostConstructAspect::class, $container->getAspect(PostConstructAspect::class));
        $this->assertInstanceOf(PrePublicMethodAspect::class, $container->getAspect(PrePublicMethodAspect::class));
        $this->assertInstanceOf(PostPublicMethodAspect::class, $container->getAspect(PostPublicMethodAspect::class));

        $this->setExpectedException(\OutOfBoundsException::class);
        $this->assertInstanceOf(PropertyWriteAspect::class, $container->getAspect(PropertyWriteAspect::class));
    }

    /**
     * @runInSeparateProcess
     */
    public function testWillAllowDisablingAllAspects()
    {
        $container = $this->buildContainer([]);

        foreach ($this->allExpectedAspects as $aspectName) {
            try {
                $container->getAspect($aspectName);

                $this->fail('No exception was thrown');
            } catch (\OutOfBoundsException $exception) {
                // empty catch, on purpose
            }
        }
    }

    /**
     * @runInSeparateProcess
     */
    public function testWillEnableJailingPublicMethods()
    {
        $container = $this->buildContainer([
            StrictPhpKernel::JAIL_PUBLIC_METHOD_PARAMETERS,
        ]);

        $this->assertInstanceOf(PrePublicMethodAspect::class, $container->getAspect(PrePublicMethodAspect::class));
    }

    /**
     * @runInSeparateProcess
     */
    public function testWillEnablePropertyWriteTypeChecks()
    {
        $container = $this->buildContainer([
            StrictPhpKernel::CHECK_PROPERTY_WRITE_TYPE,
        ]);

        $this->assertInstanceOf(PropertyWriteAspect::class, $container->getAspect(PropertyWriteAspect::class));
    }

    /**
     * @runInSeparateProcess
     */
    public function testWillEnablePropertyWriteImmutabilityChecks()
    {
        $container = $this->buildContainer([
            StrictPhpKernel::CHECK_PROPERTY_WRITE_IMMUTABILITY,
        ]);

        $this->assertInstanceOf(PropertyWriteAspect::class, $container->getAspect(PropertyWriteAspect::class));
    }

    /**
     * @param string[] $enabled
     *
     * @return AspectContainer
     */
    private function buildContainer(array $enabled = null)
    {
        if (is_array($enabled)) {
            $strictPhp = StrictPhpKernel::bootstrap($this->baseConfig, $enabled);
        } else {
            $strictPhp = StrictPhpKernel::bootstrap($this->baseConfig);
        }

        $container = $strictPhp->getContainer();

        $this->assertInstanceOf(AspectContainer::class, $container);
        $this->assertArraySubset($this->baseConfig, $strictPhp->getOptions());

        return $container;
    }
}
