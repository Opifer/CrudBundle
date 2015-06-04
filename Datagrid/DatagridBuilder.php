<?php

namespace Opifer\CrudBundle\Datagrid;

use Doctrine\ORM\QueryBuilder;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Opifer\CrudBundle\Datagrid\Column\Column;
use Opifer\CrudBundle\Datagrid\Cell\Type\CellTypeInterface;
use Opifer\CrudBundle\Entity\ListView;
use Opifer\CrudBundle\Pagination\Paginator;

class DatagridBuilder implements DatagridBuilderInterface
{
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    protected $container;

    /** @var \Opifer\CrudBundle\Datagrid\DatagridMapper */
    protected $mapper;

    /** @var \Opifer\CrudBundle\Datagrid\Datagrid */
    protected $datagrid;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function create($source)
    {
        $this->mapper = new DatagridMapper();
        $this->datagrid = new Datagrid();
        $this->datagrid->setSource($source);
        if ($this->getRequest()->get('view')) {
            $this->findAndSetView($this->getRequest()->get('view'));
        } else {
            $this->datagrid->setView($this->createView($source));
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addColumn($property, $cell, array $options = array(), $sortable = true)
    {
        if (!$cell instanceof CellTypeInterface) {
            $cell = $this->container->get('opifer.crud.cell_registry')->getCellType($cell);
        }

        $column = new Column();
        $column->setProperty($property);
        $column->setCellType($cell);
        $column->setAttributes($options);
        $column->setSortable($sortable);

        if (isset($options['label'])) {
            $column->setLabel($options['label']);
        }

        $this->mapper->addColumn($column);

        return $this;
    }

    /**
     * Create view
     *
     * @param string $source
     *
     * @return ListView
     */
    protected function createView($source)
    {
        $view = new ListView();
        $view->setEntity($source);
        $vars = $this->getRequest()->request->get('listview');
        if (null !== $conditions = $vars['conditions']) {
            $view->setConditions($conditions);
        }

        return $view;
    }

    /**
     * Add a view
     *
     * @param string $slug
     */
    public function setView(ListView $view)
    {
        $this->datagrid->setView($view);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function where($where)
    {
        $this->mapper->addWhere($where);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setParameter($parameter, $value)
    {
        $this->mapper->addParameter($parameter, $value);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function build()
    {
        $columns = $this->getColumns();
        $columns = $this->mapper->mapColumns($columns);
        $this->datagrid->setColumns($columns);

        $rows = $this->getRows();
        $rows = $this->mapper->mapRows($rows, $columns);
        $this->datagrid->setRows($rows);

        $this->handleViewForm();

        $views = $this->getViewRepository()->findByEntity(get_class($this->datagrid->getSource()));
        $this->datagrid->setViews($views);

        return $this->datagrid;
    }

    /**
     * Handle the view form
     *
     * @return  DatagridBuilder
     */
    public function handleViewForm()
    {
        $viewForm = $this->container->get('opifer.crud.listview_manager')->handleForm($this->getRequest(), $this->datagrid);
        $this->datagrid->setViewForm($viewForm);

        return $this;
    }

    /**
     * Set the item limit
     *
     * @param integer $limit
     */
    public function setLimit($limit)
    {
        $this->mapper->setLimit($limit);

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

        return $this->mapper->getLimit();
    }

    /**
     * Set the current page number
     *
     * @param integer $page
     */
    public function setPage($page)
    {
        $this->mapper->setPage($page);

        return $this;
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

        return $this->mapper->getPage();
    }

    /**
     * Determine which columns to use, based on the view
     *
     * @return array
     */
    public function getColumns()
    {
        if (count($this->mapper->getColumns())) {
            $columns = $this->mapper->getColumns();
        } elseif ($this->getRequest()->get('view')) {
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

    /**
     * Set rows
     *
     * @param \IteratorAggregate $rows
     * @return DatagridBuilder
     */
    public function setRows(\IteratorAggregate $rows)
    {
        $this->mapper->setRows($rows);

        return $this;
    }

    /**
     * Get rows
     *
     * @return \IteratorAggregate
     */
    public function getRows()
    {
        if ($this->mapper->getRows() === null) {
            $paginator = new Paginator($this->getRowQuery(), $this->getLimit(), $this->getPage());
            $this->mapper->setRows($paginator);
            $this->datagrid->setPaginator($paginator);
        }

        return $this->mapper->getRows();
    }


    /**
     * Find and set a view by its slug
     *
     * @param string $view
     *
     * @return ListView
     */
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
     * Get the view object
     *
     * @return ListView
     */
    public function getView()
    {
        return $this->datagrid->getView();
    }

    /**
     * Determine what rows to use, based on the view
     */
    public function getRowQuery()
    {
        $viewBuilder = $this->container->get('opifer.crud.view_builder');

        $source = $this->datagrid->getSource();

        if ($filterfields = $this->getRequest()->get('filterfields')) {
            $qb = $viewBuilder->any($source, $filterfields);
        } elseif ($this->getRequest()->get('view')) {
            $qb = $viewBuilder->getRowQuery($this->datagrid->getView()->getConditions(), $source);
        } elseif (($postVars = $this->getRequest()->request->get('listview')) && ($postVars['conditions'] != '')) {
            $conditions = $this->container->get('jms_serializer')
                ->deserialize($postVars['conditions'], 'Opifer\RulesEngine\Rule\Rule', 'json');

            $qb = $viewBuilder->getRowQuery($conditions, $source);
        } else {
            $sourceRepository = $this->container->get('doctrine')->getRepository(get_class($source));

            $qb = $sourceRepository->createQueryBuilder('a');
        }

        foreach ($this->mapper->getWheres() as $where) {
            $qb->andWhere($where);
        }

        foreach ($this->mapper->getParameters() as $parameter => $value) {
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

            $sortParts = explode('.', $query->get('sort'));
            $sort = ucwords(str_replace(array('-', '_'), ' ', $sortParts[1]));
            $sort = str_replace(' ', '', $sort);
            $sort = lcfirst($sort);
            $sort = $sortParts[0] . '.' . $sort;

            $rowQuery->orderBy($sort, $direction);
        } else {
            $rowQuery->orderBy('a.id', 'DESC');
        }

        return $rowQuery;
    }

    /**
     * Get the mapper
     *
     * @return DatagridMapper
     */
    public function getMapper()
    {
        return $this->mapper;
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
        return $this->container->get('opifer.crud.listview_manager')->getRepository();
    }
}
