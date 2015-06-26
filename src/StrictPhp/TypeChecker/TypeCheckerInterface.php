<?php

namespace StrictPhp\TypeChecker;

use phpDocumentor\Reflection\Type;

interface TypeCheckerInterface
{
    /**
     * @param Type $type
     *
     * @return bool
     */
    public function canApplyToType(Type $type);

    /**
     * @param mixed $value
     * @param Type  $type
     *
     * @return bool
     */
    public function validate($value, Type $type);

    /**
     * @param mixed $value
     * @param Type $type
     *
     * @return void
     *
     * @throws \ErrorException|\Exception
     */
    public function simulateFailure($value, Type $type);
}
