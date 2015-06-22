<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

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
