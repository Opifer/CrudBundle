<?php

namespace Opifer\CrudBundle\Datagrid\Cell\Type;

use Opifer\CrudBundle\Datagrid\Column\Column;

interface CellTypeInterface
{
    /**
     * Get the cell data
     *
     * @param  mixed  $row
     * @param  Column $column
     *
     * @return mixed
     */
    public function getData($row, Column $column);

    /**
     * Get the view name
     *
     * @return string
     */
    public function getView();

    /**
     * The type name
     *
     * @return string
     */
    public function getName();
}
