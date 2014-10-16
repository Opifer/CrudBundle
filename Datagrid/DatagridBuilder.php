<?php

namespace Opifer\CrudBundle\Datagrid;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Opifer\CrudBundle\Datagrid\Column\Column;
use Opifer\CrudBundle\Pagination\Paginator;

class DatagridBuilder
{
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    protected $container;

    /** @var \Opifer\CrudBundle\Datagrid\DatagridMapper */
    protected $mapper;

    /** @var \Opifer\CrudBundle\Datagrid\Datagrid */
    protected $datagrid;

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
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->columns = new ArrayCollection();
        $this->mapper = new DatagridMapper();
        $this->wheres = array();
        $this->parameters = array();
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
        $this->datagrid->setViews($this->getViewRepository()->findByEntity(get_class($source)));

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

        if (isset($options['label'])) {
            $column->setLabel($options['label']);
        }

        if (isset($options['function']) && $options['function'] instanceof \Closure) {
            $column->setClosure($options['function']);
        }

        $this->columns->add($column);

        return $this;
    }

    /**
     * Add a view
     *
     * @param string $slug
     */
    public function setView($slug = '')
    {
        if ($slug !== '') {
            $view = $this->getViewRepository()->findOneBy([
                'slug'   => $slug,
                'entity' => get_class($this->datagrid->getSource())
            ]);
            $this->datagrid->setView($view);
        }

        return $this;
    }

    /**
     * Adds a where clause
     *
     * @param string $where
     */
    public function where($where)
    {
        $this->wheres[] = $where;

        return $this;
    }

    public function setParameter($parameter, $value)
    {
        $this->parameters[$parameter] = $value;

        return $this;
    }

    /**
     * Set options
     *
     * @param array $options
     */
    public function setOptions($options = array())
    {
        $this->datagrid->setOptions($options);

        return $this;
    }

    /**
     * Build the actual datagrid
     *
     * @return \Opifer\CrudBundle\Datagrid\Datagrid
     */
    public function build()
    {
        $columns = $this->getColumns();
        $columns = $this->mapper->mapColumns($columns);
        $this->datagrid->setColumns($columns);

        $rowQuery = $this->getRowQuery();

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
        if ($this->getRequest()->get('limit')) {
            return $this->getRequest()->get('limit');
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
        if ($this->getRequest()->get('page')) {
            return $this->getRequest()->get('page');
        }

        return $this->page;
    }

    /**
     * Determine which columns to use, based on the view
     *
     * @return array
     */
    public function getColumns()
    {
        if (count($this->columns)) {
            $columns = $this->columns;
        } elseif (null !== $view = $this->datagrid->getView()) {
            $columns = json_decode($view->getColumns(), true);
        } else {
            if (count($this->datagrid->getViews())) {
                $columns = json_decode(array_pop($this->datagrid->getViews())->getColumns(), true);
            } else {
                $columns = $this->container->get('opifer.crud.view_builder')->allColumns($this->datagrid->getSource());
            }
        }

        return $columns;
    }

    public function findAndSetView($view)
    {
        $view = $this->getViewRepository()->findOneBy([
            'entity' => get_class($this->datagrid->getSource()),
            'slug'   => $view
        ]);

        $this->datagrid->setView($view);

        return $view;
    }

    /**
     * Determine which rows to use, based on the view
     */
    public function getRowQuery()
    {
        $viewBuilder = $this->container->get('opifer.crud.view_builder');

        $source = $this->datagrid->getSource();

        if ($filterfields = $this->getRequest()->request->get('filterfields')) {
            $qb = $viewBuilder->any($source, $filterfields);
        } elseif (($postVars = $this->getRequest()->request->get('listview')) && ($postVars['conditions'] != '')) {
            $conditions = $this->container->get('jms_serializer')
                ->deserialize($postVars['conditions'], 'Opifer\RulesEngine\Rule\Rule', 'json');

            $qb = $viewBuilder->getRowQuery($conditions, $source);
        } elseif (null !== $view = $this->datagrid->getView()) {
            $qb = $viewBuilder->getRowQuery($view->getConditions(), $source);
        } else {
            $sourceRepository = $this->container->get('doctrine')->getRepository(get_class($source));

            $qb = $sourceRepository->createQueryBuilder('a');
        }

        foreach ($this->wheres as $where) {
            $qb->andWhere($where);
        }

        foreach ($this->parameters as $parameter => $value) {
            $qb->setParameter($parameter, $value);
        }

        return $this->sortRows($qb);
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
    public function sortRows(QueryBuilder $rowQuery)
    {
        $query = $this->getRequest()->query;
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
     * Get the current request
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->container->get('request');
    }

    /**
     * Get the view repository
     *
     * @return ListViewRepository
     */
    public function getViewRepository()
    {
        return $this->container->get('doctrine')->getRepository('Opifer\CrudBundle\Entity\ListView');
    }
}
