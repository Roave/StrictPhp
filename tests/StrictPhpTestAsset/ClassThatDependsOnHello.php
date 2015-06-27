<?php

namespace StrictPhpTestAsset;

class ClassThatDependsOnHello
{
    /**
     * @param HelloInterface $helloSayer
     * @param                $name
     *
     * @return string
     */
    public function sayHello(HelloInterface $helloSayer, $name)
    {
        return $helloSayer->hello($name);
    }

    /**
     * @param HelloInterface $helloSayer
     *
     * @return string
     */
    public function doSomethingElseWithHello(HelloInterface $helloSayer)
    {
        /* @var $helloSayer ClassWithHelloImplementationAndAdditionalMethod */
        return $helloSayer->otherMethod();
    }
}
