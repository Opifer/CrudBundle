<?php

namespace Opifer\CrudBundle\Datagrid;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\PropertyAccess\PropertyAccess;

use Opifer\CrudBundle\Datagrid\Cell\Type\TextCell;
use Opifer\CrudBundle\Datagrid\Cell\Cell;
use Opifer\CrudBundle\Datagrid\Cell\CellFactory;
use Opifer\CrudBundle\Datagrid\Column\Column;
use Opifer\CrudBundle\Datagrid\Row\Row;

/**
 * Datagrid Mapper
 *
 * Holds all the datagrid settings during the datagrid building process.
 */
class DatagridMapper
{
    /** @var \Doctrine\Common\Collections\ArrayCollection */
    protected $columns;
    
    /** @var \IteratorAggregate */
    protected $rows;

    /** @var integer */
    protected $page = 1;

    /** @var integer */
    protected $limit = 25;

    /** @var array */
    protected $wheres;

    /** @var array */
    protected $parameters;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->columns = new ArrayCollection();
        $this->wheres = [];
        $this->parameters = [];
    }

    /**
     * Add a column
     *
     * @param Column $column
     */
    public function addColumn(Column $column)
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * Get columns
     *
     * @return ArrayCollection
     */
    public function getColumns()
    {
        return $this->columns;
    }
    
    /**
     * Set rows
     *
     * @param \IteratorAggregate $rows
     * @return DatagridMapper
     */
    public function setRows(\IteratorAggregate $rows)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * Get rows
     *
     * @return \IteratorAggregate
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Set page
     *
     * @param integer $page
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set limit
     *
     * @param integer $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get limit
     *
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Add where
     *
     * @param string $where
     */
    public function addWhere($where)
    {
        $this->wheres[] = $where;

        return $this;
    }

    /**
     * Get wheres
     *
     * @return array
     */
    public function getWheres()
    {
        return $this->wheres;
    }

    /**
     * Add a parameter
     *
     * @param string $parameter
     * @param string $value
     */
    public function addParameter($parameter, $value)
    {
        $this->parameters[$parameter] = $value;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Transform the columns to be used within the grid
     *
     * @param  array $columns
     * @return array
     */
    public function mapColumns($columns)
    {
        if ($columns instanceof ArrayCollection) {
            return $columns;
        }

        $collection = new ArrayCollection();

        foreach ($columns as $columnArray) {
            if (isset($columnArray['options']['label'])) {
                $label = $columnArray['options']['label'];
            } else {
                $label = ucfirst(trim(preg_replace(
                    // (1) Replace special chars by spaces
                    // (2) Insert spaces between lower-case and upper-case
                    ['/[_\W]+/', '/([a-z])([A-Z])/'],
                    [' ', '$1 $2'],
                    $columnArray['property']
                )));
            }

            $column = new Column();
            $column->setProperty($columnArray['property']);
            $column->setCellType(new TextCell());
            $column->setLabel($label);

            $collection->add($column);
        }

        return $collection;
    }

    /**
     * Transform the rows to be used within the grid
     *
     * @param  [array|Traversable] $rows
     * @return array
     */
    public function mapRows($rows, $columns)
    {
        if (!is_array($rows) && !$rows instanceof \Traversable) {
            throw new \InvalidArgumentException(
                'The grid items should be an array or a \Traversable.'
            );
        }

        $collection = new ArrayCollection();
        $accessor = PropertyAccess::getPropertyAccessor();

        foreach ($rows as $originalRow) {
            $row = new Row();
            $row->setObject($originalRow);
            $row->setId($originalRow->getId());
            $row->setName($originalRow->getId());

            foreach ($columns as $column) {
                $cellFactory = new CellFactory();
                $cell = $cellFactory->create($column, $originalRow);
                $row->addCell($cell);
            }
            $collection->add($row);
        }

        return $collection;
    }
}
