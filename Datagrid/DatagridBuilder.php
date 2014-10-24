<?php

namespace Opifer\CrudBundle\Datagrid;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Opifer\CrudBundle\Datagrid\Column\Column;
use Opifer\CrudBundle\Entity\ListView;
use Opifer\CrudBundle\Form\Type\ListViewType;
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
        if ($this->getRequest()->get('view')) {
            $this->findAndSetView($this->getRequest()->get('view'));
        } else {
            $this->datagrid->setView($this->createView($source));
        }

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
     * Adds a where clause
     *
     * @param string $where
     */
    public function where($where)
    {
        $this->wheres[] = $where;

        return $this;
    }

    /**
     * Set parameter
     *
     * @param string $parameter
     * @param mixed  $value
     */
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

        if (!$this->handleViewForm()) {
            throw new \Exception('The view could not be solved because the form was invalid.');
        }

        $views = $this->getViewRepository()->findByEntity(get_class($this->datagrid->getSource()));
        $this->datagrid->setViews($views);

        return $this->datagrid;
    }

    /**
     * Handle the viewform
     *
     * @return boolean
     */
    protected function handleViewForm()
    {
        $columns = $this->container->get('opifer.crud.entity_helper')->getAllProperties(
            $this->datagrid->getView()->getEntity()
        );

        // Clone the view, so the current view won't get changed by the empty view form
        $view = clone $this->datagrid->getView();
        
        $type = new ListViewType($this->datagrid->getSource(), $columns);
        $viewForm = $this->container->get('form.factory')->create($type, $view);
        $viewForm->handleRequest($this->getRequest());

        if ($viewForm->get('save')->isClicked()) {
            if ($viewForm->isValid()) {
                $view = $this->container->get('opifer.crud.listview_manager')->handleForm($viewForm->getData());
                $this->getRequest()->request->set('view', $view->getSlug());
            } else {
                return false;
            }
        }

        $viewForm = $viewForm->createView();
        $this->datagrid->setViewForm($viewForm);

        return true;
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

        if ($filterfields = $this->getRequest()->request->get('filterfields')) {
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
