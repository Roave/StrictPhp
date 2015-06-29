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

namespace StrictPhp\Reflection;

use ReflectionClass;
use ReflectionProperty;

final class AllProperties
{
    /**
     * @param ReflectionClass $class
     *
     * @return ReflectionProperty[]
     */
    public function __invoke(ReflectionClass $class)
    {
        return array_merge(
            [],
            [],
            ...array_map([$this, 'propertiesOfClass'], $this->allHierarchyClasses($class))
        );
    }

    /**
     * @param ReflectionClass $class
     *
     * @return ReflectionProperty[]
     */
    private function propertiesOfClass(ReflectionClass $class)
    {
        $className = $class->getName();

        return array_values(array_filter(
            $class->getProperties(),
            function (ReflectionProperty $property) use ($className) {
                return $property->getDeclaringClass()->getName() === $className;
            }
        ));
    }

    /**
     * @param ReflectionClass $class
     *
     * @return ReflectionClass[] all the classes in the hierarchy, starting from the given one as leaf
     */
    private function allHierarchyClasses(ReflectionClass $class)
    {
        return ($parent = $class->getParentClass())
            ? array_merge([$class], $this->allHierarchyClasses($parent))
            : [$class];
    }
}
