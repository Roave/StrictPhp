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

namespace StrictPhp\TypeChecker;

use phpDocumentor\Reflection\Type;

final class ApplyTypeChecks
{
    /**
     * @var TypeCheckerInterface[]
     */
    private $typeCheckers;

    /**
     * @param TypeCheckerInterface ...$typeCheckers
     */
    public function __construct(TypeCheckerInterface ...$typeCheckers)
    {
        $this->typeCheckers = $typeCheckers;
    }

    /**
     * @param \phpDocumentor\Reflection\Type[] $allowedTypes
     * @param mixed    $value
     *
     * @return void
     *
     * @throws \ErrorException|\Exception
     */
    public function __invoke(array $allowedTypes, $value)
    {
        // @todo turn into functional?
        $validCheckers = [];

        foreach ($allowedTypes as $type) {
            foreach ($this->typeCheckers as $typeChecker) {
                if ($typeChecker->canApplyToType($type)) {
                    $validCheckers[] = [$typeChecker, $type];
                }
            }
        }

        $applicableCheckers = array_filter(
            $validCheckers,
            function (array $typeCheckerData) use ($value) {
                /* @var $checker TypeCheckerInterface */
                /* @var $type Type */
                list($checker, $type) = $typeCheckerData;

                return $checker->validate($value, $type);
            }
        );

        array_map(
            function (array $typeCheckerData) use ($value) {
                /* @var $checker TypeCheckerInterface */
                /* @var $type Type */
                list($checker, $type) = $typeCheckerData;

                $checker->simulateFailure($value, $type);
            },
            $applicableCheckers ?: $validCheckers
        );
    }
}
