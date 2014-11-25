<?php

namespace Opifer\CrudBundle\Datagrid\Grid;

use Opifer\CrudBundle\Datagrid\DatagridBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class AbstractGrid implements GridInterface
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
            'actions' => [
                'edit'   => ['template' => 'OpiferCrudBundle:Datagrid:action_edit.html.twig'],
                'delete' => ['template' => 'OpiferCrudBundle:Datagrid:action_delete.html.twig'],
            ]
        ]);
    }
}
