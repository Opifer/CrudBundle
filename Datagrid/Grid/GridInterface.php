<?php

namespace Opifer\CrudBundle\Datagrid\Grid;

use Opifer\CrudBundle\Datagrid\DatagridBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

interface GridInterface
{
    /**
     * Builds the grid
     *
     * @param  DatagridBuilderInterface $builder
     *
     * @return void
     */
    public function buildGrid(DatagridBuilderInterface $builder);

    /**
     * Configure grid options
     *
     * @param  OptionsResolverInterface $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolverInterface $resolver);
}
