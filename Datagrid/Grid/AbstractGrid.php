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

    }
}
