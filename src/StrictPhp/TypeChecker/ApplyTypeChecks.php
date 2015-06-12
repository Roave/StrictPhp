<?php

namespace StrictPhp\TypeChecker;

final class ApplyTypeChecks
{
    /**
     * @param string[] $allowedTypes
     * @param mixed    $value
     *
     * @return void
     *
     * @todo this has to be split into multiple different type checkers, one for each possible allowed type
     *       each checker should be a separate object that can "match" a given value and can emulate a match failure
     */
    public function __invoke(array $allowedTypes, $value)
    {
        $matchingCheckers = [];

        foreach ($allowedTypes as $type) {
            $baseType = strtolower($type);

            if ('callable' === $baseType) {
                $matchingCheckers[] = [$this, 'checkCallable'];
            }

            if ('array' === $baseType) {
                $matchingCheckers[] = [$this, 'checkArray'];
            }

            if ('string' === $baseType) {
                $matchingCheckers[] = [$this, 'checkString'];
            }

            if ('integer' === $baseType) {
                $matchingCheckers[] = [$this, 'checkInteger'];
            }

            if ('float' === $baseType) {
                $matchingCheckers[] = [$this, 'checkFloat'];
            }

            // ...
        }

        if (empty($matchingCheckers) && ! empty($allowedTypes)) {
            // could not find fitting type checkers - execute the first checker that could apply?
        }

        // should actually sort by "nearest" and execute only the first one

        foreach ($matchingCheckers as $matchingChecker) {
            $matchingChecker($value);
        }
    }

    private function checkCallable(callable $value)
    {
    }

    private function checkArray(array $value)
    {
    }

    private function checkString($value)
    {
        if (! is_string($value)) {
            throw new \UnexpectedValueException(sprintf('Expecting string, %s given', gettype($value)));
        }
    }

    private function checkInteger($value)
    {
        if (! is_integer($value)) {
            throw new \UnexpectedValueException(sprintf('Expecting integer, %s given', gettype($value)));
        }
    }

    private function checkFloat($value)
    {
        if (! is_float($value)) {
            throw new \UnexpectedValueException(sprintf('Expecting float, %s given', gettype($value)));
        }
    }
}
