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
     * @param string $type
     *
     * @return bool
     */
    public function validate($value, $type);

    /**
     * @param mixed $value
     * @param string $type
     *
     * @return void
     *
     * @throws \ErrorException|\Exception
     */
    public function simulateFailure($value, $type);
}
