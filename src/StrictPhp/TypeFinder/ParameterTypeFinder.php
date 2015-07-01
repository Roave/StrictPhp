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

namespace StrictPhp\TypeFinder;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag\ParamTag;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\Self_;
use phpDocumentor\Reflection\Types\Static_;
use ReflectionParameter;

final class ParameterTypeFinder
{
    /**
     * @param ReflectionParameter $reflectionParameter
     * @param string              $contextClass
     *
     * @return Type[]
     */
    public function __invoke(ReflectionParameter $reflectionParameter, $contextClass)
    {
        $typeResolver = new TypeResolver();
        $context      = (new ContextFactory())->createFromReflector($reflectionParameter);

        return array_map(
            function (Type $type) use ($reflectionParameter, $contextClass) {
                return $this->expandSelfAndStaticTypes($type, $reflectionParameter, $contextClass);
            },
            array_unique(array_filter(array_merge(
                [],
                [],
                ...array_map(
                    function (ParamTag $varTag) use ($typeResolver, $context) {
                        return array_map(
                            function ($type) use ($typeResolver, $context) {
                                return $typeResolver->resolve($type, $context);
                            },
                            $varTag->getTypes()
                        );
                    },
                    $this->getParamTagsForParameter($reflectionParameter, $context)
                )
            )))
        );
    }

    /**
     * Replaces "self", "$this" and "static" types with the corresponding runtime versions
     *
     * @todo may be removed if PHPDocumentor provides a runtime version of the types VO
     *
     * @param Type                $type
     * @param ReflectionParameter $reflectionParameter
     * @param string              $contextClass
     *
     * @return Type
     */
    private function expandSelfAndStaticTypes(Type $type, ReflectionParameter $reflectionParameter, $contextClass)
    {
        if ($type instanceof Self_) {
            return new Object_(new Fqsen('\\' . $reflectionParameter->getDeclaringClass()->getName()));
        }

        if ($type instanceof Static_) {
            return new Object_(new Fqsen('\\' . $contextClass));
        }

        return $type;
    }

    /**
     * @param ReflectionParameter $reflectionParameter
     * @param Context             $context
     *
     * @return ParamTag[]
     */
    private function getParamTagsForParameter(ReflectionParameter $reflectionParameter, Context $context)
    {
        $reflectionFunction = $reflectionParameter->getDeclaringFunction();
        $parameterName      = $reflectionParameter->getName();

        return array_filter(
            (new DocBlock(
                $reflectionFunction,
                new DocBlock\Context($context->getNamespace(), $context->getNamespaceAliases())
            ))
                ->getTagsByName('param'),
            function (ParamTag $paramTag) use ($parameterName) {
                return ltrim($paramTag->getVariableName(), '$') === $parameterName;
            }
        );
    }
}
