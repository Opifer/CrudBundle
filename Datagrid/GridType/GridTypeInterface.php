<?php

namespace Opifer\CrudBundle\Datagrid\GridType;

interface GridTypeInterface
{
    /**
     * Builds the grid
     *
     * @return \Opifer\CrudBundle\Datagrid\Datagrid
     */
    public function buildGrid();
}
