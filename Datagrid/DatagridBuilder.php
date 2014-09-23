<?php

namespace Opifer\CrudBundle\Datagrid;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Opifer\CrudBundle\Datagrid\Column\Column;
use Opifer\CrudBundle\Datagrid\Row\Row;
use Opifer\CrudBundle\Pagination\Paginator;

class DatagridBuilder
{
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    protected $container;

    /** @var \Opifer\CrudBundle\Datagrid\Datagrid */
    protected $datagrid;

    /** @var integer */
    protected $page = 1;

    /** @var integer */
    protected $limit = 25;

    protected $columns;

    protected $mapper;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->columns = new ArrayCollection();
        $this->mapper = new DatagridMapper();
    }

    /**
     * Create the datagrid
     *
     * @param object $source
     *
     * @return void
     */
    public function create($source)
    {
        $this->datagrid = new Datagrid();
        $this->datagrid->setSource($source);
        $this->datagrid->setColumnFilters($this->getFilterRepository()->columnFilters(get_class($source)));
        $this->datagrid->setRowFilters($this->getFilterRepository()->rowFilters(get_class($source)));

        return $this;
    }

    /**
     * Add a column
     *
     * @param string $child
     * @param string $type
     * @param array  $options
     */
    public function addColumn($property, $type = 'text', array $options = array())
    {
        $column = new Column();
        $column->setProperty($property);
        $column->setType($type);
        $column->setLabel($options['label']);

        $this->columns->add($column);

        return $this;
    }

    public function addRow($id)
    {
        // @todo
    }

    /**
     * Add a column filter
     *
     * @param string $filter
     */
    public function addColumnFilter($filter)
    {
        $this->datagrid->setSelectedColumnFilter($filter);

        return $this;
    }

    /**
     * add a row filter
     *
     * @param string $filter
     */
    public function addRowFilter($filter)
    {
        $this->datagrid->setSelectedRowFilter($filter);

        return $this;
    }

    /**
     * Build the actual datagrid
     *
     * @return \Opifer\CrudBundle\Datagrid\Datagrid
     */
    public function build()
    {
        if (null !== $this->datagrid->getSelectedColumnFilter()) {
            $filter = $this->datagrid->getSelectedColumnFilter();
        } else {
            $filter = 'default';
        }

        $columns = $this->getColumns($filter);
        $columns = $this->mapper->mapColumns($columns);
        $this->datagrid->setColumns($columns);

        if (null !== $this->datagrid->getSelectedRowFilter()) {
            $filter = $this->datagrid->getSelectedRowFilter();
        } else {
            $filter = 'default';
        }

        $rowQuery = $this->getRowQuery($filter);

        $paginator = new Paginator($rowQuery, $this->getLimit(), $this->getPage());
        $this->datagrid->setPaginator($paginator);
        $this->datagrid->setRows($this->mapper->mapRows($paginator, $columns));

        return $this->datagrid;
    }

    /**
     * Set the item limit
     *
     * @param integer $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get the item limit based on the query parameter. If it's not set,
     * use the fallback.
     *
     * @return integer
     */
    public function getLimit()
    {
        if ($this->container->get('request')->get('limit')) {
            return $this->container->get('request')->get('limit');
        }

        return $this->limit;
    }

    /**
     * Set the current page number
     *
     * @param integer $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * Get the current page number based on the query parameter. If it's not set,
     * use the fallback.
     *
     * @return integer
     */
    public function getPage()
    {
        if ($this->container->get('request')->get('page')) {
            return $this->container->get('request')->get('page');
        }

        return $this->page;
    }

    /**
     * Determine which columns to use, based on the filter
     *
     * @param string
     *
     * @return Datagrid
     */
    public function getColumns($filter = 'default')
    {
        $this->datagrid->setSelectedColumnFilter($filter);

        if (count($this->columns)) {
            $columns = $this->columns;
        } elseif ($filter == 'default') {
            if (count($this->datagrid->getColumnFilters())) {
                $columns = json_decode(array_pop($this->datagrid->getColumnFilters())->getColumns(), true);
            } else {
                $columns = $this->container->get('opifer.crud.filter_builder')->allColumns($this->datagrid->getSource());
            }
        } else {
            $filter = $this->getFilterRepository()->oneColumnFilter($filter, get_class($this->datagrid->getSource()));

            $columns = json_decode($filter->getColumns(), true);
        }

        return $columns;
    }

    /**
     * Determine which rows to use, based on the filter
     *
     * @param string $filter
     */
    public function getRowQuery($filter = 'default')
    {
        $this->datagrid->setSelectedRowFilter($filter);

        $source = $this->datagrid->getSource();

        $filterBuilder = $this->container->get('opifer.crud.filter_builder');
        if ($this->container->get('request')->request->get('filterfields')) {
            $rows = $filterBuilder->any($source, $this->container->get('request')->request->get('filterfields'));
        } elseif ($filter !== 'default') {
            $filter = $this->getFilterRepository()->oneRowFilter($filter, $source);

            $rows = $filterBuilder->getRowQuery($filter->getConditions(), $source);
        } else {
            $sourceRepository = $this->container->get('doctrine')->getRepository(get_class($source));

            $rows = $sourceRepository->createQueryBuilder('a');
        }

        return $this->sortRows($rows);
    }

    /**
     * Sort rows
     *
     * If a specific sort parameter is given, order the results by that parameter
     * and the given direction.
     *
     * @param QueryBuilder $rowQuery
     *
     * @return QueryBuilder
     */
    public function sortRows($rowQuery)
    {
        $query = $this->container->get('request')->query;
        if (null !== $query->get('sort')) {
            if (null !== $query->get('direction')) {
                $direction = $query->get('direction');
            } else {
                $direction = 'asc';
            }
            $rowQuery->orderBy($query->get('sort'), $direction);
        }

        return $rowQuery;
    }

    /**
     * Get the filter repository
     *
     * @return EntityRepository
     */
    public function getFilterRepository()
    {
        return $this->container->get('doctrine')->getRepository('Opifer\CrudBundle\Entity\CrudFilter');
    }
}
