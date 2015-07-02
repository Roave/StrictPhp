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
use phpDocumentor\Reflection\DocBlock\Tag\VarTag;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\Self_;
use phpDocumentor\Reflection\Types\Static_;
use ReflectionProperty;

final class PropertyTypeFinder
{
    /**
     * @param ReflectionProperty $reflectionProperty
     * @param string             $contextClass
     *
     * @return \phpDocumentor\Reflection\Type[]
     */
    public function __invoke(ReflectionProperty $reflectionProperty, $contextClass)
    {
        $typeResolver = new TypeResolver();
        $context      = (new ContextFactory())->createFromReflector($reflectionProperty);

        return array_map(
            function (Type $type) use ($reflectionProperty, $contextClass) {
                return $this->expandSelfAndStaticTypes($type, $reflectionProperty, $contextClass);
            },
            array_unique(array_filter(array_merge(
                [],
                ...array_map(
                    function (VarTag $varTag) use ($typeResolver, $context) {
                        return array_map(
                            function ($type) use ($typeResolver, $context) {
                                return $typeResolver->resolve($type, $context);
                            },
                            $varTag->getTypes()
                        );
                    },
                    (new DocBlock(
                        $reflectionProperty,
                        new DocBlock\Context($context->getNamespace(), $context->getNamespaceAliases())
                    ))
                        ->getTagsByName('var')
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
     * @param ReflectionProperty $reflectionProperty
     * @param string             $contextClass
     *
     * @return Type
     */
    private function expandSelfAndStaticTypes(Type $type, ReflectionProperty $reflectionProperty, $contextClass)
    {
        if ($type instanceof Self_) {
            return new Object_(new Fqsen('\\' . $reflectionProperty->getDeclaringClass()->getName()));
        }

        if ($type instanceof Static_) {
            return new Object_(new Fqsen('\\' . $contextClass));
        }

        return $type;
    }
}
