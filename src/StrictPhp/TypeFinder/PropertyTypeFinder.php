<?php

namespace StrictPhp\TypeFinder;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag\VarTag;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\ContextFactory;
use ReflectionProperty;

final class PropertyTypeFinder
{
    /**
     * @param ReflectionProperty $reflectionProperty
     *
     * @return \phpDocumentor\Reflection\Type[]
     */
    public function __invoke(ReflectionProperty $reflectionProperty)
    {
        $typeResolver = new TypeResolver();
        $context      = (new ContextFactory())->createFromReflector($reflectionProperty->getDeclaringClass());

        return array_unique(array_filter(array_merge(
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
                // note: this will not compute imports for us! Need a better alternative!
                (new DocBlock($reflectionProperty))->getTagsByName('var')
            )
        )));
    }
}
