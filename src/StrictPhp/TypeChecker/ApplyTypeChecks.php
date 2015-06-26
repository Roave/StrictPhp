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
            function (array $typeChecker) use ($value) {
                /* @var $typeChecker TypeCheckerInterface[]|string[] */
                return $typeChecker[0]->validate($value, $typeChecker[1]);
            }
        );

        array_map(
            function (array $typeChecker) use ($value) {
                /* @var $typeChecker TypeCheckerInterface[]|string[] */
                $typeChecker[0]->simulateFailure($value, $typeChecker[1]);
            },
            $applicableCheckers ?: $validCheckers
        );
    }
}
