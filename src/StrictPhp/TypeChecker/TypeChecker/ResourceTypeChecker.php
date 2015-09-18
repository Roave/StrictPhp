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
use phpDocumentor\Reflection\Types\Resource;
use StrictPhp\TypeChecker\TypeCheckerInterface;

final class ResourceTypeChecker implements TypeCheckerInterface
{
    /**
     * {@inheritDoc}
     */
    public function canApplyToType(Type $type)
    {
        return $type instanceof Resource;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function validate($value, Type $type)
    {
        if (! $type instanceof Resource) {
            throw new \InvalidArgumentException(sprintf(
                'Unsupported type "%s" given, expecting "%s"',
                get_class($type),
                Resource::class
            ));
        }

        return is_resource($value);
    }

    /**
     * {@inheritDoc}
     */
    public function simulateFailure($value, Type $type)
    {
        if (! $this->validate($value, $type)) {
            throw new \ErrorException(sprintf(
                'Unsupported type "%s" given, expecting "%s"',
                gettype($type),
                'resource'
            ));
        }
    }
}
