<?php

namespace StrictPhp;

use Go\Core\AspectContainer;
use Go\Core\AspectKernel;
use StrictPhp\AccessChecker\PropertyWriteImmutabilityChecker;
use StrictPhp\AccessChecker\PropertyWriteTypeChecker;
use StrictPhp\Aspect\PropertyWriteAspect;

class StrictPhpKernel extends AspectKernel
{
    /**
     * {@inheritDoc}
     */
    protected function configureAop(AspectContainer $container)
    {
        $container->registerAspect(new PropertyWriteAspect(
            new PropertyWriteImmutabilityChecker(),
            new PropertyWriteTypeChecker()
        ));
    }
}
