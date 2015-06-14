<?php

namespace StrictPhp;

use Go\Aop\Features;
use Go\Core\AspectContainer;
use Go\Core\AspectKernel;
use StrictPhp\AccessChecker\PropertyWriteImmutabilityChecker;
use StrictPhp\AccessChecker\PropertyWriteTypeChecker;
use StrictPhp\Aspect\PostConstructStateCheck;
use StrictPhp\Aspect\PropertyWriteAspect;

class StrictPhpKernel extends AspectKernel
{
    /**
     * {@inheritDoc}
     *
     * @override
     */
    protected $options = ['features' => Features::INTERCEPT_INITIALIZATIONS];

    /**
     * {@inheritDoc}
     */
    protected function configureAop(AspectContainer $container)
    {
        $container->registerAspect(new PostConstructStateCheck());
        $container->registerAspect(new PropertyWriteAspect(
            new PropertyWriteImmutabilityChecker(),
            new PropertyWriteTypeChecker()
        ));
    }
}
