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

use Go\Aop\Intercept\FieldAccess;
use phpDocumentor\Reflection\DocBlock;

final class PropertyWriteImmutabilityChecker
{
    /**
     * @param FieldAccess $access
     *
     * @return void
     *
     * @throws \RuntimeException
     */
    public function __invoke(FieldAccess $access)
    {
        if (! $that = $access->getThis()) {
            return;
        }

        if (FieldAccess::WRITE !== $access->getAccessType()) {
            return;
        }

        $field = $access->getField();

        $field->setAccessible(true);

        // simplistic check - won't check for multiple assignments of "null" to a "null" valued field
        if (null === ($currentValue = $field->getValue($that))) {
            return;
        }

        if (! (new DocBlock($field))->getTagsByName('immutable')) {
            return;
        }

        $newValue = $access->getValueToSet();

        throw new \RuntimeException(sprintf(
            'Trying to overwrite property %s#$%s of object %s#%s with a value of type "%s".'
            . ' The property was already given a value of type %s',
            $field->getDeclaringClass()->getName(),
            $field->getName(),
            get_class($that),
            spl_object_hash($that),
            is_object($newValue) ? get_class($newValue) : gettype($newValue),
            is_object($currentValue) ? get_class($currentValue) : gettype($currentValue)
        ));
    }
}
