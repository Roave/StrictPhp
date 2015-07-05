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

namespace StrictPhp\Aspect;

use Go\Aop\Aspect;
use Go\Aop\Intercept\FieldAccess;
use Go\Lang\Annotation as Go;

final class PropertyWriteAspect implements Aspect
{
    /**
     * @var callable[]
     */
    private $propertyWriteCheckers;

    /**
     * @param callable ...$propertyWriteCheckers
     */
    public function __construct(callable ...$propertyWriteCheckers)
    {
        $this->propertyWriteCheckers = $propertyWriteCheckers;
    }

    /**
     * @Go\Before("access(public|protected **->*)")
     *
     * @param FieldAccess $access
     *
     * @return mixed
     */
    public function beforePropertyAccess(FieldAccess $access)
    {
        if (FieldAccess::WRITE !== $access->getAccessType()) {
            return $access->proceed();
        }

        foreach ($this->propertyWriteCheckers as $checker) {
            $checker($access);
        }

        return $access->proceed();
    }
}
