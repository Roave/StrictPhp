<?php

namespace StrictPhp\TypeChecker;

interface TypeCheckerInterface
{
    /**
     * @param string $type
     *
     * @return bool
     */
    public function canApplyToType($type);

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function validate($value);

    /**
     * @param mixed $value
     *
     * @return void
     *
     * @throws \ErrorException|\Exception
     */
    public function simulateFailure($value);
}
