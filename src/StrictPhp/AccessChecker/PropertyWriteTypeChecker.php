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
use StrictPhp\TypeChecker\ApplyTypeChecks;
use StrictPhp\TypeChecker\TypeChecker\CallableTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\GenericObjectTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\MixedTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\NullTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\StringTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker;
use StrictPhp\TypeFinder\PropertyTypeFinder;

final class PropertyWriteTypeChecker
{
    /**
     * @param FieldAccess $access
     *
     * @return void
     *
     * @throws \ErrorException|\Exception
     */
    public function __invoke(FieldAccess $access)
    {
        if (FieldAccess::WRITE !== $access->getAccessType()) {
            return;
        }

        $that         = $access->getThis();
        $contextClass = $that ? get_class($that) : $access->getField()->getDeclaringClass()->getName();

        $baseCheckers = [
            new IntegerTypeChecker(),
            new CallableTypeChecker(),
            new StringTypeChecker(),
            new GenericObjectTypeChecker(),
            new ObjectTypeChecker(),
            new MixedTypeChecker(),
            new NullTypeChecker(),
        ];

        (new ApplyTypeChecks(
            new TypedTraversableChecker(...$baseCheckers),
            ...$baseCheckers
        ))->__invoke(
            (new PropertyTypeFinder())->__invoke($access->getField(), $contextClass),
            $access->getValueToSet()
        );
    }
}
