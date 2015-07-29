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

namespace StrictPhp;

use Go\Core\AspectContainer;
use Go\Core\AspectKernel;
use InterNations\Component\TypeJail\Factory\JailFactory;
use StrictPhp\AccessChecker\ObjectStateChecker;
use StrictPhp\AccessChecker\ParameterInterfaceJailer;
use StrictPhp\AccessChecker\ParameterTypeChecker;
use StrictPhp\AccessChecker\PropertyWriteImmutabilityChecker;
use StrictPhp\AccessChecker\PropertyWriteTypeChecker;
use StrictPhp\AccessChecker\ReturnTypeChecker;
use StrictPhp\Aspect\PostConstructAspect;
use StrictPhp\Aspect\PostPublicMethodAspect;
use StrictPhp\Aspect\PrePublicMethodAspect;
use StrictPhp\Aspect\PropertyWriteAspect;
use StrictPhp\TypeChecker\ApplyTypeChecks;
use StrictPhp\TypeChecker\TypeChecker\BooleanTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\CallableTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\GenericObjectTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\IntegerTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\MixedTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\NullTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\ObjectTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\StringTypeChecker;
use StrictPhp\TypeChecker\TypeChecker\TypedTraversableChecker;
use StrictPhp\TypeFinder\PropertyTypeFinder;

/**
 * Note: not extensible on purpose. This is a bootstrapper/kernel that is totally procedural.
 *
 * Overriding
 */
final class StrictPhpKernel extends AspectKernel
{
    const CHECK_STATE_AFTER_CONSTRUCTOR_CALL   = 'CHECK_STATE_AFTER_PUBLIC_METHOD_CALL';
    const JAIL_PUBLIC_METHOD_PARAMETERS        = 'JAIL_PUBLIC_METHOD_PARAMETERS';
    const CHECK_STATE_AFTER_PUBLIC_METHOD_CALL = 'CHECK_STATE_AFTER_PUBLIC_METHOD_CALL';
    const CHECK_PUBLIC_METHOD_PARAMETER_TYPE   = 'CHECK_PUBLIC_METHOD_PARAMETER_TYPE';
    const CHECK_PUBLIC_METHOD_RETURN_TYPE      = 'CHECK_PUBLIC_METHOD_RETURN_TYPE';
    const CHECK_PROPERTY_WRITE_IMMUTABILITY    = 'CHECK_PROPERTY_WRITE_IMMUTABILITY';
    const CHECK_PROPERTY_WRITE_TYPE            = 'CHECK_PROPERTY_WRITE_TYPE';

    /**
     * {@inheritDoc}
     */
    protected function configureAop(AspectContainer $container)
    {
        // empty: configuration should come from elsewhere
    }

    /**
     * @param mixed[]  $options  {@see \Go\Core\AspectKernel::init()}
     * @param string[] $features
     *
     * @return self
     */
    public static function bootstrap(
        $options = [],
        $features = [
            self::CHECK_STATE_AFTER_CONSTRUCTOR_CALL,
            self::CHECK_STATE_AFTER_PUBLIC_METHOD_CALL,
            self::CHECK_PUBLIC_METHOD_PARAMETER_TYPE,
            self::CHECK_PUBLIC_METHOD_RETURN_TYPE,
        ]
    ) {
        $enabled = array_flip($features);
        $kernel  = self::getInstance();

        $kernel->init($options);

        array_map(
            [$kernel->getContainer(), 'registerAspect'],
            array_merge(
                self::buildPostConstructCallAspects($enabled),
                self::buildPrePublicMethodCallAspects($enabled),
                self::buildPostPublicMethodCallAspects($enabled),
                self::buildPropertyAccessAspects($enabled)
            )
        );

        return $kernel;
    }

    /**
     * @param mixed[] $enabled indexed by string
     *
     * @return \Go\Aop\Aspect[]
     */
    private static function buildPostConstructCallAspects(array $enabled)
    {
        if (! isset($enabled[self::CHECK_STATE_AFTER_CONSTRUCTOR_CALL])) {
            return [];
        }

        return [new PostConstructAspect(self::buildObjectStateChecker())];
    }

    /**
     * @param mixed[] $enabled indexed by string
     *
     * @return \Go\Aop\Aspect[]
     */
    private static function buildPrePublicMethodCallAspects(array $enabled)
    {
        $prePublicMethodIncerceptors = [];

        if (isset($enabled[self::JAIL_PUBLIC_METHOD_PARAMETERS])) {
            $prePublicMethodIncerceptors[] = new ParameterInterfaceJailer(new JailFactory());
        }

        if (isset($enabled[self::CHECK_PUBLIC_METHOD_PARAMETER_TYPE])) {
            $prePublicMethodIncerceptors[] = new ParameterTypeChecker(self::buildTypeChecker());
        }

        return $prePublicMethodIncerceptors ? [new PrePublicMethodAspect(...$prePublicMethodIncerceptors)] : [];
    }

    /**
     * @param mixed[] $enabled indexed by string
     *
     * @return \Go\Aop\Aspect[]
     */
    private static function buildPostPublicMethodCallAspects(array $enabled)
    {
        $postPublicMethodInterceptors = [];

        if (isset($enabled[self::CHECK_STATE_AFTER_PUBLIC_METHOD_CALL])) {
            $postPublicMethodInterceptors[] = self::buildObjectStateChecker();
        }

        if (isset($enabled[self::CHECK_PUBLIC_METHOD_RETURN_TYPE])) {
            $postPublicMethodInterceptors[] = new ReturnTypeChecker(self::buildTypeChecker());
        }

        return $postPublicMethodInterceptors ? [new PostPublicMethodAspect(...$postPublicMethodInterceptors)] : [];
    }

    /**
     * @param mixed[] $enabled indexed by string
     *
     * @return \Go\Aop\Aspect[]
     */
    private static function buildPropertyAccessAspects(array $enabled)
    {
        $propertyAccessCheckers = [];

        if (isset($enabled[self::CHECK_PROPERTY_WRITE_IMMUTABILITY])) {
            $propertyAccessCheckers[] = new PropertyWriteImmutabilityChecker();
        }

        if (isset($enabled[self::CHECK_PROPERTY_WRITE_TYPE])) {
            $propertyAccessCheckers[] = new PropertyWriteTypeChecker();
        }

        return $propertyAccessCheckers ? [new PropertyWriteAspect(...$propertyAccessCheckers)] : [];
    }

    /**
     * @return ObjectStateChecker
     */
    private static function buildObjectStateChecker()
    {
        return new ObjectStateChecker(self::buildTypeChecker(), new PropertyTypeFinder());
    }

    /**
     * @return ApplyTypeChecks
     */
    private static function buildTypeChecker()
    {
        $typeCheckers = [
            new IntegerTypeChecker(),
            new CallableTypeChecker(),
            new StringTypeChecker(),
            new GenericObjectTypeChecker(),
            new ObjectTypeChecker(),
            new MixedTypeChecker(),
            new BooleanTypeChecker(),
            new NullTypeChecker(),
        ];

        // intentionally looping data structure
        $typeCheckers[] = new TypedTraversableChecker(...$typeCheckers);

        return new ApplyTypeChecks(...$typeCheckers);
    }
}
