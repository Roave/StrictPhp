<?php

namespace StrictPhpTestAsset;

class ClassWithIncorrectlyInitializedParentClassProperties extends ParentClassWithInitializingConstructor
{
    /**
     * @var array
     */
    private $property;

    /**
     * Child constructor (doesn't call parent constructor on purpose (causes a failure)
     */
    public function __construct()
    {
        // empty constructor (on purpose)
        $this->property = ['the child class array'];
    }
}
