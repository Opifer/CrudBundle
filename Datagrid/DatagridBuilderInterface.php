<?php

namespace Opifer\CrudBundle\Datagrid;

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
     *
     * @return $this
     */
    public function addColumn($property, $cell, array $options = []);

    /**
     * Adds a where clause
     *
     * @param string $where
     *
     * @return $this
     */
    public function where($where);

    /**
     * Set parameter
     *
     * @param string $parameter
     * @param mixed  $value
     *
     * @return $this
     */
    public function setParameter($parameter, $value);

    /**
     * Build the actual datagrid
     *
     * @return Datagrid
     */
    public function build();
}
