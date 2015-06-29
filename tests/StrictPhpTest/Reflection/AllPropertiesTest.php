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
