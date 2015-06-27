<?php

namespace StrictPhpTestAsset;

class ParentClassWithInitializingConstructor
{
    /**
     * @var array
     */
    private $property;

    /**
     * Constructor that correctly initializes this object
     */
    public function __construct()
    {
        $this->property = ['the parent class array'];
    }
}
