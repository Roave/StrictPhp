<?php

namespace StrictPhp;

use Go\Core\AspectContainer;
use Go\Core\AspectKernel;
use StrictPhp\Aspect\ImmutablePropertyCheck;
use StrictPhp\Aspect\PropertyWriteTypeCheck;

class StrictPhpKernel extends AspectKernel
{
    /**
     * {@inheritDoc}
     */
    protected function configureAop(AspectContainer $container)
    {
        $container->registerAspect(new PropertyWriteTypeCheck());
        $container->registerAspect(new ImmutablePropertyCheck());
    }
}
