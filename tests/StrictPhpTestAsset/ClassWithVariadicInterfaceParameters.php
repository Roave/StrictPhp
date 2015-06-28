<?php

namespace StrictPhpTestAsset;

class ClassWithVariadicInterfaceParameters
{
    /**
     * @return void
     */
    public function methodWithNoParameters()
    {
    }

    /**
     * @param HelloInterface $hello
     *
     * @return void
     */
    public function methodWithSingleParameter(HelloInterface $hello)
    {
    }

    /**
     * @param HelloInterface ...$hello
     *
     * @return void
     */
    public function methodWithVariadicSingleParameter(HelloInterface ...$hello)
    {
    }

    /**
     * @param HelloInterface $hello1
     * @param HelloInterface ...$hello2
     *
     * @return void
     */
    public function methodWithParameterAndVariadicSingleParameter(HelloInterface $hello1, HelloInterface ...$hello2)
    {
    }

    /**
     * @param HelloInterface $hello1
     * @param mixed          $hello2
     *
     * @return void
     */
    public function methodWithInterfacedAndNonInterfacedParameters(HelloInterface $hello1, $hello2)
    {
    }

    /**
     * @param mixed          $hello1
     * @param HelloInterface $hello2
     *
     * @return void
     */
    public function methodWithNonInterfacedAndInterfacedParameters($hello1, HelloInterface $hello2)
    {
    }

    /**
     * @param ClassWithHelloImplementationAndAdditionalMethod $hello1
     * @param HelloInterface                                  $hello2
     *
     * @return void
     */
    public function methodWithConcreteTypeHintAndInterfaceHint(
        ClassWithHelloImplementationAndAdditionalMethod $hello1,
        HelloInterface $hello2
    ) {
    }

    /**
     * @param HelloInterface|null $hello1
     *
     * @return void
     */
    public function methodWithInterfacedHintAndDefaultNull(HelloInterface $hello1 = null)
    {
    }
}
