<?php

namespace StrictPhp\TypeFinder;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag\VarTag;
use ReflectionProperty;

final class PropertyTypeFinder
{
    /**
     * @param ReflectionProperty $reflectionProperty
     *
     * @return string[]
     */
    public function __invoke(ReflectionProperty $reflectionProperty)
    {
        return array_unique(array_merge(
            [],
            [],
            ...array_map(
                function (VarTag $varTag) {
                    return $varTag->getTypes();
                },
                // note: this will not compute imports for us! Need a better alternative!
                (new DocBlock($reflectionProperty))->getTagsByName('var')
            )
        ));
    }
}
