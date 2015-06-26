<?php

namespace StrictPhp\TypeChecker\TypeChecker;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Mixed;
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
    public function canApplyToType(Type $type)
    {
        // @todo validate also key type!
        return $type instanceof Array_
            && $this->getCheckersApplicableToType($type->getValueType());
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value, Type $type)
    {

        return $type instanceof Array_
            && (($value instanceof \Traversable) || is_array($value))
            && $this->getCheckersValidForType($value, $type->getValueType());
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function simulateFailure($value, Type $type)
    {
        if (! $type instanceof Array_) {
            throw new \InvalidArgumentException(sprintf(
                'Expecting type of type "%s", "%s" given',
                Array_::class,
                get_class($type)
            ));
        }

        if (! $value instanceof \Traversable) {
            $callback = function (array $value) {
            };

            $callback($value);
        }

        $subType = $type->getValueType();

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
     * @param Type $type
     *
     * @return TypeCheckerInterface[]
     */
    private function getCheckersApplicableToType(Type $type)
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
     * @param Type   $type
     *
     * @return TypeCheckerInterface[]
     */
    private function getCheckersValidForType($value, Type $type)
    {
        return array_filter(
            $this->typeCheckers,
            function (TypeCheckerInterface $typeChecker) use ($value, $type) {
                return $typeChecker->validate($value, $type);
            }
        );
    }
}
