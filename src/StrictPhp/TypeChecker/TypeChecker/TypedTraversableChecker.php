<?php

namespace StrictPhp\TypeChecker\TypeChecker;

use StrictPhp\TypeChecker\TypeCheckerInterface;

final class TypedTraversableChecker implements TypeCheckerInterface
{
    /**
     * @var TypeCheckerInterface[]
     */
    private $typeCheckers;

    /**
     * @param TypeCheckerInterface ...$typeCheckers
     */
    public function __construct(TypeCheckerInterface ...$typeCheckers)
    {
        /* @var $typeCheckers TypeCheckerInterface[] */
        $this->typeCheckers = array_merge($typeCheckers, [$this]);
    }

    /**
     * {@inheritDoc}
     */
    public function canApplyToType($type)
    {
        return substr($type, -2) === '[]'
            && $this->getCheckersApplicableToType(substr($type, 0, -2));
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value, $type)
    {
        return substr($type, -2) === '[]'
            && (($value instanceof \Traversable) || is_array($value))
            && $this->getCheckersValidForType($value, substr($type, 0, -2));
    }

    /**
     * {@inheritDoc}
     */
    public function simulateFailure($value, $type)
    {
        $subType = substr($type, 0, -2);

        array_map(
            function (TypeCheckerInterface $typeChecker) use ($value, $subType) {
                foreach ($value as $singleValue) {
                    $typeChecker->simulateFailure($singleValue, $subType);
                }
            },
            $this->getCheckersApplicableToType($subType)
        );
    }

    /**
     * @param string $type
     *
     * @return TypeCheckerInterface[]
     */
    private function getCheckersApplicableToType($type)
    {
        return array_filter(
            $this->typeCheckers,
            function (TypeCheckerInterface $typeChecker) use ($type) {
                return $typeChecker->canApplyToType($type);
            }
        );
    }

    /**
     * @param mixed  $value
     * @param string $type
     *
     * @return TypeCheckerInterface[]
     */
    private function getCheckersValidForType($value, $type)
    {
        return array_filter(
            $this->typeCheckers,
            function (TypeCheckerInterface $typeChecker) use ($value, $type) {
                return $typeChecker->validate($value, $type);
            }
        );
    }
}
