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

namespace StrictPhp\AccessChecker;

use ReflectionClass;
use ReflectionProperty;
use StrictPhp\Reflection\AllProperties;

final class ObjectStateChecker
{
    /**
     * @var callable
     */
    private $applyTypeChecks;

    /**
     * @var callable
     */
    private $findTypes;

    /**
     * @param callable $applyTypeChecks
     * @param callable $findTypes
     */
    public function __construct(callable $applyTypeChecks, callable $findTypes)
    {
        $this->applyTypeChecks = $applyTypeChecks;
        $this->findTypes       = $findTypes;
    }

    /**
     * @param object $object
     * @param string $scope  scope of the state checks
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @throws \ErrorException
     */
    public function __invoke($object, $scope)
    {
        if (! is_object($object)) {
            throw new \InvalidArgumentException(sprintf(
                'Provided argument must be an object, %s given',
                gettype($object)
            ));
        }

        array_map(
            function (ReflectionProperty $property) use ($object) {
                $property->setAccessible(true);

                $this->checkProperty($property, $property->getValue($object));
            },
            (new AllProperties())->__invoke(new ReflectionClass($scope))
        );
    }

    /**
     * @param ReflectionProperty $property
     * @param mixed              $value
     *
     * @return void
     *
     * @throws \Exception|\ErrorException
     */
    private function checkProperty(
        ReflectionProperty $property,
        $value
    ) {
        $typeChecker = $this->applyTypeChecks;
        $findTypes   = $this->findTypes;

        $typeChecker($findTypes($property, $property->getDeclaringClass()->getName()), $value);
    }
}
