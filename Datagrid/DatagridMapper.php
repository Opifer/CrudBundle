<?php

namespace Opifer\CrudBundle\Datagrid;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\PropertyAccess\PropertyAccess;

use Opifer\CrudBundle\Datagrid\Column\Column;
use Opifer\CrudBundle\Datagrid\Row\Cell;
use Opifer\CrudBundle\Datagrid\Row\Row;

class DatagridMapper
{
    /** @var \Doctrine\Common\Collections\ArrayCollection */
    protected $columns;

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
            $column->setType($columnArray['type']);
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
            $row->setName($originalRow->getId());

            foreach ($columns as $column) {
                $cell = new Cell();

                // Set a row name value
                if (in_array($column->getProperty(), ['username', 'name', 'title'])) {
                    $row->setName($accessor->getValue($originalRow, $column->getProperty()));
                }

                // Handle the raw value
                if ($column->getClosure() instanceof \Closure) {
                    $closure = $column->getClosure();
                    $value = $accessor->getValue($originalRow, $column->getProperty());
                    $value = $closure($value);
                } elseif (substr($column->getProperty(), -strlen('.count')) === '.count') {
                    // in case of one-to-many or many-to-many relations, show
                    // the count of related rows
                    $explode = explode('.', $column->getProperty());
                    $explode[0] = 'get' . ucfirst($explode[0]);
                    $value = $originalRow->$explode[0]()->$explode[1]();
                } else {
                    try {
                        $value = $accessor->getValue($originalRow, $column->getProperty());
                    } catch (\Exception $e) {
                        $value = null;
                    }
                }

                // Handle the generated value
                if ($value instanceof \DateTime) {
                    $value = $value->format('d-m-Y');
                } elseif (is_array($value)) {
                    $value = json_encode($value);
                }

                if (null !== $column->getType()) {
                    $cell->setType($column->getType());
                }

                $cell->setValue($value);
                $cell->setProperty($column->getProperty());
                
                if ($column->getAttributes()) {
                    $cell->setAttributes($column->getAttributes());
                }

                $row->addCell($cell);
            }

            $row->setId($originalRow->getId());
            $collection->add($row);
        }

        return $collection;
    }
}
