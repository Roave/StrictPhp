<?php

namespace StrictPhp\Reflection;

use ReflectionClass;
use ReflectionProperty;

final class AllProperties
{
    /**
     * @param ReflectionClass $class
     *
     * @return ReflectionProperty[]
     */
    public function __invoke(ReflectionClass $class)
    {
        return array_merge(
            [],
            [],
            ...array_map([$this, 'propertiesOfClass'], $this->allHierarchyClasses($class))
        );
    }

    /**
     * @param ReflectionClass $class
     *
     * @return ReflectionProperty[]
     */
    private function propertiesOfClass(ReflectionClass $class)
    {
        $className = $class->getName();

        return array_values(array_filter(
            $class->getProperties(),
            function (ReflectionProperty $property) use ($className) {
                return $property->getDeclaringClass()->getName() === $className;
            }
        ));
    }

    /**
     * @param ReflectionClass $class
     *
     * @return ReflectionClass[] all the classes in the hierarchy, starting from the given one as leaf
     */
    private function allHierarchyClasses(ReflectionClass $class)
    {
        return ($parent = $class->getParentClass())
            ? array_merge([$class], $this->allHierarchyClasses($parent))
            : [$class];
    }
}
