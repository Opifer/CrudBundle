<?php

namespace Opifer\CrudBundle\Datagrid;

use Opifer\CrudBundle\Datagrid\Cell\Type\CellTypeInterface;

interface DatagridBuilderInterface
{
    /**
     * Create the datagrid
     *
     * @param object $source
     *
     * @return DatagridBuilder
     */
    public function create($source);

    /**
     * Add a column
     *
     * @param string $child
     * @param mixed  $cell     string|CellTypeInterface 
     * @param array  $options
     */
    public function addColumn($property, $cell, array $options = []);

    /**
     * Adds a where clause
     *
     * @param string $where
     */
    public function where($where);

    /**
     * Set parameter
     *
     * @param string $parameter
     * @param mixed  $value
     */
    public function setParameter($parameter, $value);

    /**
     * Set options
     *
     * @param array $options
     */
    public function setOptions($options = []);

    /**
     * Build the actual datagrid
     *
     * @return Datagrid
     */
    public function build();
}
