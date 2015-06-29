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

namespace StrictPhp\AccessChecker;

use Go\Aop\Intercept\MethodInvocation;
use Go\Lang\Annotation as Go;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Context;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\Self_;
use phpDocumentor\Reflection\Types\Static_;
use ReflectionMethod;
use ReflectionParameter;
use StrictPhp\TypeChecker\ApplyTypeChecks;
use StrictPhp\TypeChecker\TypeChecker\CallableTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\GenericObjectTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\MixedTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\NullTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\StringTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker;

final class ParameterTypeChecker
{
    /**
     * @param MethodInvocation $methodInvocation
     *
     * @return void
     *
     * @throws \ErrorException
     */
    public function __invoke(MethodInvocation $methodInvocation)
    {
        $method               = $methodInvocation->getMethod();
        $paramTags            = $this->getParamTags($method);
        $reflectionParameters = $method->getParameters();

        foreach ($methodInvocation->getArguments() as $argumentIndex => $argument) {
            $this->applyTypeChecks(
                $this->getParameterDocblockType(
                    $method,
                    get_class($methodInvocation->getThis()),
                    $paramTags,
                    $reflectionParameters,
                    $argumentIndex
                ),
                $argument
            );
        }
    }

    /**
     * @param Type[] $types
     * @param mixed  $value
     *
     * @return void
     *
     * @throws \ErrorException
     */
    private function applyTypeChecks(array $types, $value)
    {
        $baseCheckers = [
            new IntegerTypeChecker(),
            new CallableTypeChecker(),
            new StringTypeChecker(),
            new GenericObjectTypeChecker(),
            new ObjectTypeChecker(),
            new MixedTypeChecker(),
            new NullTypeChecker(),
        ];

        (new ApplyTypeChecks(
            new TypedTraversableChecker(...$baseCheckers),
            ...$baseCheckers
        ))->__invoke($types, $value);
    }

    /**
     * @param ReflectionMethod        $method
     * @param string                  $contextClass
     * @param DocBlock\Tag\ParamTag[] $paramTags
     * @param \ReflectionParameter[]  $reflectionParameters
     * @param int                     $index
     *
     * @return Type[]
     */
    private function getParameterDocblockType(ReflectionMethod $method, $contextClass, array $paramTags, array $reflectionParameters, $index)
    {
        // @TODO consider variadic arguments here

        if (! isset($reflectionParameters[$index])) {
            return [];
        }

        if (! $paramTag = $this->getParamTagByName($paramTags, $reflectionParameters[$index]->getName())) {
            return [];
        }

        return $this->expandParameterTypes($paramTag, $method, $contextClass);
    }

    /**
     * @param DocBlock\Tag\ParamTag $param
     * @param ReflectionMethod      $reflectionMethod
     * @param string                $contextClass
     *
     * @return Type[]
     */
    private function expandParameterTypes(DocBlock\Tag\ParamTag $param, ReflectionMethod $reflectionMethod, $contextClass)
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
                    function (DocBlock\Tag\ParamTag $paramTag) use ($typeResolver, $context) {
                        return array_map(
                            function ($type) use ($typeResolver, $context) {
                                return $typeResolver->resolve($type, $context);
                            },
                            $paramTag->getTypes()
                        );
                    },
                    [$param] // @TODO could be more than one @param
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
        if ($type instanceof Self_) {
            return new Object_(new Fqsen('\\' . $reflectionMethod->getDeclaringClass()->getName()));
        }

        if ($type instanceof Static_) {
            return new Object_(new Fqsen('\\' . $contextClass));
        }

        return $type;
    }

    /**
     * @param DocBlock\Tag\ParamTag[] $paramTags
     * @param string                  $name
     *
     * @return DocBlock\Tag\ParamTag|null
     */
    private function getParamTagByName(array $paramTags, $name)
    {
        foreach ($paramTags as $paramTag) {
            if (ltrim($paramTag->getVariableName(), '$') === $name) {
                return $paramTag;
            }
        }

        return null;
    }

    /**
     * @param ReflectionMethod $reflectionMethod
     *
     * @return DocBlock\Tag\ParamTag[]
     */
    private function getParamTags(ReflectionMethod $reflectionMethod)
    {
        return (new DocBlock(
            $reflectionMethod,
            new Context($reflectionMethod->getDeclaringClass()->getNamespaceName())
        ))
            ->getTagsByName('param');
    }
}
