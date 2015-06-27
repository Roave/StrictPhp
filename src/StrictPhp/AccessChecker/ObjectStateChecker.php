<?php

namespace StrictPhp\AccessChecker;

use Go\Lang\Annotation as Go;
use ReflectionClass;
use ReflectionProperty;
use StrictPhp\Reflection\AllProperties;

final class ObjectStateChecker
{
    /**
     * @var callable
     */
    private $applyTypeChecks;

    /**
     * @var callable
     */
    private $findTypes;

    /**
     * @param callable $applyTypeChecks
     * @param callable $findTypes
     */
    public function __construct(callable $applyTypeChecks, callable $findTypes)
    {
        $this->applyTypeChecks = $applyTypeChecks;
        $this->findTypes       = $findTypes;
    }

    /**
     * @param object $object
     * @param string $scope  scope of the state checks
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @throws \ErrorException
     */
    public function __invoke($object, $scope)
    {
        if (! is_object($object)) {
            throw new \InvalidArgumentException(sprintf(
                'Provided argument must be an object, %s given',
                gettype($object)
            ));
        }

        array_map(
            function (ReflectionProperty $property) use ($object) {
                $property->setAccessible(true);

                $this->checkProperty($property, $property->getValue($object));
            },
            (new AllProperties())->__invoke(new ReflectionClass($scope))
        );
    }

    /**
     * @param ReflectionProperty $property
     * @param mixed              $value
     *
     * @return void
     *
     * @throws \Exception|\ErrorException
     */
    private function checkProperty(
        ReflectionProperty $property,
        $value
    ) {
        $typeChecker = $this->applyTypeChecks;
        $findTypes   = $this->findTypes;

        $typeChecker($findTypes($property, $property->getDeclaringClass()->getName()), $value);
    }
}
