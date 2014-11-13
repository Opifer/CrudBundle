<?php

namespace Opifer\CrudBundle\Datagrid\Cell;

use Opifer\CrudBundle\Datagrid\Cell\Type\CellTypeInterface;

class CellRegistry
{
    /** @var  array */
    protected $cells;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cells = [];
    }

    /**
     * Add cell type
     *
     * @param CellTypeInterface $cell
     * @param string            $alias
     */
    public function addCellType(CellTypeInterface $cell, $alias)
    {
        if (isset($this->cells[$alias])) {
            throw new \Exception(sprintf('Cell with alias %s already exists', $alias));
        }

        $this->cells[$alias] = $cell;
    }

    /**
     * Get a cell type by its alias
     *
     * @param  string $alias
     *
     * @return CellTypeInterface
     */
    public function getCellType($alias)
    {
        return $this->cells[$alias];
    }

    /**
     * Get all registered cell types
     *
     * @return array
     */
    public function getCellTypes()
    {
        return $this->cells;
    }
}
