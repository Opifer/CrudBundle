<?php

namespace Opifer\CrudBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Opifer\CrudBundle\DependencyInjection\Compiler\CellCompilerPass;

class OpiferCrudBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CellCompilerPass());
    }
}
