<?php

namespace Opifer\CrudBundle\Datagrid\Cell;

use Opifer\CrudBundle\Datagrid\Column\Column;
use Opifer\CrudBundle\Datagrid\Cell\Type\CellTypeInterface;
use Opifer\CrudBundle\Datagrid\Row\Row;

class CellFactory
{
    /**
     * Create a cell
     *
     * @param  Column $column
     * @param  object $row
     *
     * @return Cell
     */
    public function create(Column $column, $row)
    {
        $cellType = $column->getCellType();

        $cell = new Cell();
        $cell->setValue($cellType->getData($row, $column));
        $cell->setType($cellType->getName());
        $cell->setView($cellType->getView());
        if (null !== $column->getAttributes()) {
            $cell->setAttributes($column->getAttributes());
        }
        $cell->setProperty($column->getProperty());

        return $cell;
    }
}
