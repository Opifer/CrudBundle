<?php

namespace Opifer\CrudBundle\Datagrid\Grid;

use Opifer\CrudBundle\Datagrid\DatagridBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SimpleGrid extends AbstractGrid
{
    /**
     * {@inheritDoc}
     */
    public function buildGrid(DatagridBuilderInterface $builder)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'batch_actions' => [
                'edit' => '/'
            ],
            'actions' => [
                'edit'   => ['template' => 'OpiferCrudBundle:Datagrid:action_edit.html.twig'],
                'delete' => ['template' => 'OpiferCrudBundle:Datagrid:action_delete.html.twig'],
            ]
        ]);
    }
}
