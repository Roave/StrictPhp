<?php

namespace StrictPhpTestAsset;

class ClassWithIncorrectlyInitializingConstructor
{
    /**
     * @var array
     */
    private $property;

    public function __construct()
    {
        // empty constructor (on purpose)
    }
}
