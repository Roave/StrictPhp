<?php

namespace StrictPhpTestAsset;

class ClassWithHelloImplementationAndAdditionalMethod implements HelloInterface
{
    /**
     * {@inheritDoc}
     */
    public function hello($name)
    {
        return 'hello ' . $name;
    }

    /**
     * @return string
     */
    public function otherMethod()
    {
        return 'Other method';
    }
}
