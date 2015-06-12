<?php

namespace StrictPhp\TypeChecker;

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
     * @param string[] $allowedTypes
     * @param mixed    $value
     *
     * @return void
     *
     * @todo this has to be split into multiple different type checkers, one for each possible allowed type
     *       each checker should be a separate object that can "match" a given value and can emulate a match failure
     *
     * @throws \ErrorException|\Exception
     */
    public function __invoke(array $allowedTypes, $value)
    {
        $validCheckers = array_filter(
            $this->typeCheckers,
            function (TypeCheckerInterface $typeChecker) use ($allowedTypes) {
                return array_filter($allowedTypes, [$typeChecker, 'canApplyToType']);
            }
        );

        $applicableCheckers = array_filter(
            $validCheckers,
            function (TypeCheckerInterface $typeChecker) use ($value) {
                return $typeChecker->validate($value);
            }
        );

        array_map(
            function (TypeCheckerInterface $typeChecker) use ($value) {
                $typeChecker->simulateFailure($value);
            },
            $applicableCheckers ?: $validCheckers
        );
    }
}
