<?php

namespace StrictPhp;

use Go\Core\AspectContainer;
use Go\Core\AspectKernel;
use InterNations\Component\TypeJail\Factory\JailFactory;
use StrictPhp\AccessChecker\ObjectStateChecker;
use StrictPhp\AccessChecker\ParameterInterfaceJailer;
use StrictPhp\AccessChecker\PropertyWriteImmutabilityChecker;
use StrictPhp\AccessChecker\PropertyWriteTypeChecker;
use StrictPhp\Aspect\PostConstructAspect;
use StrictPhp\Aspect\PrePublicMethodAspect;
use StrictPhp\Aspect\PropertyWriteAspect;
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

class StrictPhpKernel extends AspectKernel
{
    /**
     * {@inheritDoc}
     */
    protected function configureAop(AspectContainer $container)
    {
        $typeCheckers = [
            new IntegerTypeChecker(),
            new CallableTypeChecker(),
            new StringTypeChecker(),
            new GenericObjectTypeChecker(),
            new ObjectTypeChecker(),
            new MixedTypeChecker(),
            new NullTypeChecker(),
        ];

        $typeCheckers[] = new TypedTraversableChecker(...$typeCheckers);

        $container->registerAspect(new PropertyWriteAspect(
            new PropertyWriteImmutabilityChecker(),
            new PropertyWriteTypeChecker()
        ));
        $container->registerAspect(new PostConstructAspect(new ObjectStateChecker(
            new ApplyTypeChecks(...$typeCheckers),
            new PropertyTypeFinder()
        )));
        $container->registerAspect(new PrePublicMethodAspect(new ParameterInterfaceJailer(new JailFactory())));
    }
}
