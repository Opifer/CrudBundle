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

    /** @var array */
    protected $views;

    /** @var string */
    protected $selectedView = 'default';

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
     * Set available views
     *
     * @param array $views
     */
    public function setViews(array $views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get all available views
     *
     * @return ArrayCollection
     */
    public function getViews()
    {
        return $this->views;
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
     * Set selected list view
     *
     * @param string $view
     */
    public function setSelectedView($view)
    {
        $this->selectedView = $view;

        return $this;
    }

    /**
     * Get selected list view
     *
     * @return string
     */
    public function getSelectedView()
    {
        return $this->selectedView;
    }

    /**
     * Set options
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
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
}
