<?php

namespace Potogan\RESTBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Potogan\RESTBundle\DependencyInjection\Compiler\GenericChainService;

class PotoganRESTBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new GenericChainService());
    }
}
