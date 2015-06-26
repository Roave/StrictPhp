<?php

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
                    (new DocBlock($reflectionProperty))->getTagsByName('var')
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
