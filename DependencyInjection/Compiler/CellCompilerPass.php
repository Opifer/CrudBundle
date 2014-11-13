<?php

namespace Opifer\CrudBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CellCompilerPass implements CompilerPassInterface
{
    /**
     * Process the compiler pass
     *
     * Adds all tagged cell types to the cell_registry
     *
     * @param Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('opifer.crud.cell_registry')) {
            return;
        }

        $definition = $container->getDefinition('opifer.crud.cell_registry');
        $taggedServices = $container->findTaggedServiceIds('opifer.cell.type');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addCellType',
                    [new Reference($id), $attributes['alias']]
                );
            }
        }
    }
}
