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

namespace StrictPhp\TypeChecker\TypeChecker;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Object_;
use StrictPhp\TypeChecker\TypeCheckerInterface;

final class ObjectTypeChecker implements TypeCheckerInterface
{
    /**
     * {@inheritDoc}
     */
    public function canApplyToType(Type $type)
    {
        return $type instanceof Object_
            && $type->getFqsen();
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function validate($value, Type $type)
    {
        if (! $type instanceof Object_) {
            throw new \InvalidArgumentException(sprintf(
                'Non-object type "%s" given, expected "%s"',
                get_class($type),
                Object_::class
            ));
        }

        if (! $fqcn = $type->getFqsen()) {
            throw new \InvalidArgumentException(sprintf(
                'The provided type of type "%s" does not have a FQCN',
                get_class($type)
            ));
        }

        $fqcnString = (string) $fqcn;

        return $value instanceof $fqcnString;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function simulateFailure($value, Type $type)
    {
        if (! $this->canApplyToType($type)) {
            throw new \InvalidArgumentException(sprintf(
                'The provided type "%s" does not refer to a valid class',
                $type
            ));
        }

        /* @var $callback callable */
        $callback = eval(sprintf('return function (%s $value) {};', $type));

        $callback($value);
    }
}
