<?php

namespace Opifer\CrudBundle\Datagrid\Grid;

use Opifer\CrudBundle\Datagrid\DatagridBuilderInterface;

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
}
