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

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Object_;
use ReflectionClass;
use ReflectionProperty;
use StrictPhp\TypeFinder\ParameterTypeFinder;
use StrictPhp\TypeFinder\PropertyTypeFinder;
use StrictPhpTestAsset\ClassWithGenericNonTypedProperty;
use StrictPhpTestAsset\ClassWithGenericStringTypedProperty;
use StrictPhpTestAsset\ClassWithMultipleParamsTypedMethodAnnotation;
use StrictPhpTestAsset\ClassWithSameTypedProperty;
use StrictPhpTestAsset\ClassWithSelfTypedProperty;
use StrictPhpTestAsset\ClassWithStaticTypedProperty;

/**
 * Tests for {@see \StrictPhp\TypeFinder\ParameterTypeFinder}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 *
 * @group Coverage
 *
 * @covers \StrictPhp\TypeFinder\ParameterTypeFinder
 */
class ParameterTypeFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider mixedAnnotationTypes
     *
     * @param string $class
     * @param int    $parameterIndex
     * @param string $methodName
     * @param string $contextClass
     * @param Type[] $expectedParameters
     */
    public function testRetrievesMethodDocblockTypes(
        $class,
        $methodName,
        $parameterIndex,
        $contextClass,
        array $expectedParameters
    ) {
        $this->assertEquals(
            $expectedParameters,
            (new ParameterTypeFinder())
                ->__invoke(new \ReflectionParameter([$class, $methodName], $parameterIndex), $contextClass)
        );
    }

    /**
     * @return mixed[][] - string with class name
     *                   - string with method name
     *                   - int with the parameter index
     *                   - string with context class name
     *                   - Type with expected fetched types
     */
    public function mixedAnnotationTypes()
    {
        return [
            'method with complex array type' => [
                ClassWithMultipleParamsTypedMethodAnnotation::class,
                'method',
                0,
                ClassWithMultipleParamsTypedMethodAnnotation::class,
                [
                    new Array_(new Object_(new Fqsen('\\' . \stdClass::class))),
                    new Array_(new Integer()),
                ],
            ],
        ];
    }
}
