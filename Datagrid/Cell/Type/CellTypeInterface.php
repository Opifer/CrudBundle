<?php

namespace Opifer\CrudBundle\Datagrid\Cell\Type;

use Opifer\CrudBundle\Datagrid\Column\Column;

interface CellTypeInterface
{
    /**
     * Get the cell data
     *
     * @param mixed  $row
     * @param Column $column
     * @param array  $attributes
     *
     * @return mixed
     */
    public function getData($row, Column $column, array $attributes);

    /**
     * Get the cell export data
     *
     * @param mixed  $row
     * @param Column $column
     * @param array  $attributes
     *
     * @return string
     */
    public function getExportData($row, Column $column, array $attributes);

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
