<?php

namespace Opifer\CrudBundle\Datagrid;

use Doctrine\Common\Collections\ArrayCollection;

use Opifer\CrudBundle\Datagrid\Column\Column;
use Opifer\CrudBundle\Datagrid\Row\Row;
use Opifer\CrudBundle\Pagination\Paginator;

/**
 * Datagrid
 *
 * Converts all data to be used in the datagrid
 */
class Datagrid
{
    /** @var Paginator */
    protected $paginator;

    /** @var ArrayCollection */
    protected $rows;

    /** @var ArrayCollection */
    protected $columns;

    /** @var array */
    protected $options = [];

    /** @var string */
    protected $template;

    /** @var ArrayCollection */
    protected $columnfilters;

    /** @var ArrayCollection */
    protected $rowfilters;

    /** @var string */
    protected $selectedRowFilter;

    /** @var string */
    protected $selectedColumnFilter;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->columns = new ArrayCollection();
        $this->rows = new ArrayCollection();
        $this->options = [
            'actions' => ['edit', 'delete']
        ];
    }

    /**
     * Set the main source for the datagrid
     *
     * @param object $source
     *
     * @return Datagrid
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get the source
     *
     * @return object
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Sets the template to be used for the list view
     * Default value is listed as protected above.
     *
     * @param string $template
     *
     * @return Datagrid
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get the grid template
     *
     * @return string
     */
    public function getTemplate()
    {
        if (!is_null($this->template)) {
            return $this->template;
        }

        return 'OpiferCrudBundle:Crud:list.html.twig';
    }

    /**
     * Add a column
     *
     * @param Column $column
     */
    public function addColumn(Column $column)
    {
        $this->columns->add($column);

        return $this;
    }

    public function setColumns(ArrayCollection $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Get all columns
     *
     * @return ArrayCollection
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Add a row
     *
     * @param Row $row
     */
    public function addRow(Row $row)
    {
        $this->rows->add($row);

        return $this;
    }

    public function setRows(ArrayCollection $rows)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * Get all rows
     *
     * @return ArrayCollection
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Get all datagrid-global options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set available column filters
     *
     * @param ArrayCollection $filters
     */
    public function setColumnFilters($filters)
    {
        $this->columnfilters = $filters;

        return $this;
    }

    /**
     * Get all available column filters
     *
     * @return ArrayCollection
     */
    public function getColumnFilters()
    {
        return $this->columnfilters;
    }

    /**
     * Set available row filters
     *
     * @param ArrayCollection $filters
     */
    public function setRowFilters($filters)
    {
        $this->rowfilters = $filters;

        return $this;
    }

    /**
     * Get all available row filters
     *
     * @return [type]
     */
    public function getRowFilters()
    {
        return $this->rowfilters;
    }

    /**
     * Set the pagination
     *
     * @param Paginator $paginator
     */
    public function setPaginator($paginator)
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * Get pagination
     *
     * @return Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * Set selected column filter
     *
     * @param string $filter
     */
    public function setSelectedColumnFilter($filter)
    {
        $this->selectedColumnFilter = $filter;

        return $this;
    }

    /**
     * Get selected column filter
     *
     * @return string
     */
    public function getSelectedColumnFilter()
    {
        return $this->selectedColumnFilter;
    }

    /**
     * Set selected row filter
     *
     * @param string $filter
     */
    public function setSelectedRowFilter($filter)
    {
        $this->selectedRowFilter = $filter;

        return $this;
    }

    /**
     * Get selected row filter
     *
     * @return string
     */
    public function getSelectedRowFilter()
    {
        return $this->selectedRowFilter;
    }
}
