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

use Go\Aop\Framework\AbstractMethodInvocation;
use Go\Aop\Support\AnnotatedReflectionMethod;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\Self_;
use phpDocumentor\Reflection\Types\Static_;
use phpDocumentor\Reflection\Types\This;
use ReflectionMethod;

/**
 * @author Jefersson Nathan <malukenho@phpse.net>
 */
final class ReturnTypeFinder
{
    /**
     * @param ReflectionMethod $reflectionMethod
     * @param $contextClass
     * @return \phpDocumentor\Reflection\Type[]
     */
    public function __invoke(ReflectionMethod $reflectionMethod, $contextClass)
    {
        $typeResolver = new TypeResolver();
        $context      = (new ContextFactory())->createFromReflector($reflectionMethod);

        return array_map(
            function (Type $type) use ($reflectionMethod, $contextClass) {
                return $this->expandSelfAndStaticTypes($type, $reflectionMethod, $contextClass);
            },
            array_unique(array_filter(array_merge(
                [],
                [],
                ...array_map(
                    function (ReturnTag $varTag) use ($typeResolver, $context) {
                        return array_map(
                            function ($type) use ($typeResolver, $context) {
                                return $typeResolver->resolve($type, $context);
                            },
                            $varTag->getTypes()
                        );
                    },
                    $this->getReturnTagForMethod($reflectionMethod, $context)
                )
            )))
        );
    }

    /**
     * Replaces "self", "$this" and "static" types with the corresponding runtime versions
     *
     * @todo may be removed if PHPDocumentor provides a runtime version of the types VO
     *
     * @param Type             $type
     * @param ReflectionMethod $reflectionMethod
     * @param string           $contextClass
     *
     * @return Type
     */
    private function expandSelfAndStaticTypes(Type $type, ReflectionMethod $reflectionMethod, $contextClass)
    {
        if ($type instanceof Self_ || $type instanceof This) {
            return new Object_(new Fqsen('\\' . $reflectionMethod->getDeclaringClass()->getName()));
        }

        if ($type instanceof Static_) {
            return new Object_(new Fqsen('\\' . $contextClass));
        }

        return $type;
    }

    /**
     * @param ReflectionMethod $reflectionMethod
     * @param Context          $context
     *
     * @return ReturnTag[]
     */
    private function getReturnTagForMethod(ReflectionMethod $reflectionMethod, Context $context)
    {
        return (new DocBlock(
                $reflectionMethod,
                new DocBlock\Context($context->getNamespace(), $context->getNamespaceAliases())
            ))
                ->getTagsByName('return');
    }
}
