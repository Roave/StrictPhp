<?php

namespace StrictPhp\TypeChecker;

use phpDocumentor\Reflection\Type;

final class ApplyTypeChecks
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
        $this->typeCheckers = $typeCheckers;
    }

    /**
     * @param \phpDocumentor\Reflection\Type[] $allowedTypes
     * @param mixed    $value
     *
     * @return void
     *
     * @throws \ErrorException|\Exception
     */
    public function __invoke(array $allowedTypes, $value)
    {
        // @todo turn into functional?
        $validCheckers = [];

        foreach ($allowedTypes as $type) {
            foreach ($this->typeCheckers as $typeChecker) {
                if ($typeChecker->canApplyToType($type)) {
                    $validCheckers[] = [$typeChecker, $type];
                }
            }
        }

        $applicableCheckers = array_filter(
            $validCheckers,
            function (array $typeCheckerData) use ($value) {
                /* @var $checker TypeCheckerInterface */
                /* @var $type Type */
                list($checker, $type) = $typeCheckerData;

                return $checker->validate($value, $type);
            }
        );

        array_map(
            function (array $typeCheckerData) use ($value) {
                /* @var $checker TypeCheckerInterface */
                /* @var $type Type */
                list($checker, $type) = $typeCheckerData;

                $checker->simulateFailure($value, $type);
            },
            $applicableCheckers ?: $validCheckers
        );
    }
}
