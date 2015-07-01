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

namespace StrictPhpTest\TypeFinder;

use Go\Aop\Framework\DynamicReflectionMethodInvocation;
use StrictPhp\TypeChecker\ApplyTypeChecks;
use StrictPhp\TypeChecker\TypeChecker;
use StrictPhp\TypeFinder\ReturnTypeFinder;
use StrictPhpTestAsset\ClassWithReturnTypeMethod;

/**
 * Tests for {@see \StrictPhp\TypeFinder\ReturnTypeFinder}
 *
 * @author Jefersson Nathan <malukenho@phpse.net>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\TypeFinder\ReturnTypeFinder
 */
class ReturnTypeFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReturnTypeFinder
     */
    private $finder;

    public function setUp()
    {
        $typeCheckers = [
            new TypeChecker\IntegerTypeChecker(),
            new TypeChecker\CallableTypeChecker(),
            new TypeChecker\StringTypeChecker(),
            new TypeChecker\GenericObjectTypeChecker(),
            new TypeChecker\ObjectTypeChecker(),
            new TypeChecker\MixedTypeChecker(),
            new TypeChecker\BooleanTypeChecker(),
            new TypeChecker\NullTypeChecker(),
        ];

        $typeCheckers[] = new TypeChecker\TypedTraversableChecker(...$typeCheckers);

        $applyTypeChecks = new ApplyTypeChecks(...$typeCheckers);

        $this->finder = new ReturnTypeFinder($applyTypeChecks);
    }

    /**
     * @dataProvider mixedAnnotationTypes
     *
     * @param $class
     * @param $methodName
     * @param $params
     */
    public function testRetrievesMethodReturnTypes($class, $methodName, $params)
    {
        $reflectionMethod = (new \ReflectionMethod($class, $methodName));

        /** @var DynamicReflectionMethodInvocation|\PHPUnit_Framework_MockObject_MockObject $methodInvocation */
        $methodInvocation = $this->getMockBuilder(DynamicReflectionMethodInvocation::class)->disableOriginalConstructor()->getMock();

        $methodInvocation->expects($this->atLeast(3))->method('getMethod')->willReturn($reflectionMethod);
        $methodInvocation->expects($this->once())->method('proceed')->willReturn(
            $reflectionMethod->invoke(new ClassWithReturnTypeMethod(), $params)[0]
        );

        $finder = $this->finder;
        $finder($methodInvocation, $class);
    }

    /**
     * @return mixed[][] - string with class name
     *                   - string with method name
     *                   - array with parameter
     */
    public function mixedAnnotationTypes()
    {
        return [
            [
                ClassWithReturnTypeMethod::class,
                'expectString',
                [
                    'Heya :D',
                ],
            ],
            [
                ClassWithReturnTypeMethod::class,
                'expectObject',
                [
                    new \DateTime(),
                ],
            ],
            [
                ClassWithReturnTypeMethod::class,
                'expectMixedDataCollection',
                [
                    [
                        true,
                        false,
                    ],
                ],
            ],
            [
                ClassWithReturnTypeMethod::class,
                'expectMixedDataCollection',
                [
                    [
                        [
                            'heya',
                        ],
                    ],
                ],
            ],
            [
                ClassWithReturnTypeMethod::class,
                'expectStdClass',
                [
                    new \StdClass
                ],
            ],
            [
                ClassWithReturnTypeMethod::class,
                'expectSelf',
                [
                    new ClassWithReturnTypeMethod(),
                ],
            ],
            [
                ClassWithReturnTypeMethod::class,
                'expectStatic',
                [
                    new ClassWithReturnTypeMethod(),
                ],
            ],
            [
                ClassWithReturnTypeMethod::class,
                'expectThis',
                [
                    new ClassWithReturnTypeMethod(),
                ],
            ],
        ];
    }
}
