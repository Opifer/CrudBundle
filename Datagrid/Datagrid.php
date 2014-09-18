<?php

namespace Opifer\CrudBundle\Datagrid;

use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;

use Opifer\CrudBundle\Pagination\Paginator;

/**
 * Datagrid
 *
 * Converts all data to be used in the datagrid
 */
class Datagrid
{
    protected $em;
    protected $fb;
    protected $request;
    protected $paginator;

    protected $entity;
    protected $rows = [];
    protected $columns = [];
    protected $options = [];
    protected $template;
    protected $columnfilters;
    protected $rowfilters;
    protected $pagination;
    protected $selectedRows;
    protected $selectedColumns;

    /**
     * Constructor
     *
     * @param EntityManager $em
     * @param FilterBuilder $fb
     */
    public function __construct(RequestStack $requestStack, EntityManager $em, FilterBuilder $fb)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->em = $em;
        $this->fb = $fb;
    }

    /**
     * Set all necessary options to render the grid
     *
     * @param  string   $entity
     * @param  array    $options
     * @return Datagrid
     */
    public function init($entity, $options = null)
    {
        $this->entity = $entity;
        if ($options !== null) {
            // Set custom options
            // Obviously needs a lot of refactoring
            $this->options = $options;
        } else {
            $this->options = [
                'actions' => ['edit', 'delete']
            ];
        }

        return $this;
    }

    /**
     * Set the columns
     *
     * @param  array    $columns
     * @param  boolean  $isFilter
     * @return Datagrid
     */
    public function setColumns($columns, $isFilter = true)
    {
        if ($isFilter) {
            $columns = $this->setColumnsByFilter($columns);
        }

        $this->columns = $this->parseColumns($columns);

        return $this;
    }

    /**
     * Set the rows
     *
     * @param mixed   $rows
     * @param integer $page
     * @param integer $limit
     * @param boolean $isFilter
     */
    public function setRows($rows, $page = 1, $limit = 10, $isFilter = true)
    {
        if ($isFilter) {
            $rows = $this->setRowsByFilter($rows, $page, $limit);
        }

        $this->rows = $this->parseRows($rows);

        return $this;
    }

    /**
     * Determine which columns to use, based on the filter
     *
     * @param string
     * @return Datagrid
     */
    public function setColumnsByFilter($filter)
    {
        $this->selectedColumns = $filter;

        if ($filter == 'default') {
            if (count($this->columnfilters)) {
                $columns = json_decode(array_pop($this->columnfilters)->getColumns(), true);
            } else {
                $columns = $this->fb->allColumns($this->entity);
            }
        } else {
            $filter = $this->getFilterRepository()->oneColumnFilter($filter, get_class($this->entity));

            $columns = json_decode($filter->getColumns(), true);
        }

        return $columns;
    }

    /**
     * Determine which rows to use, based on the filter
     *
     * @param string  $filter
     * @param integer $page
     * @param integer $limit
     */
    public function setRowsByFilter($filter, $page, $limit)
    {
        $this->selectedRows = $filter;

        if ($this->request->request->get('filterfields')) {
            $rows = $this->fb->any($this->entity, $this->request->request->get('filterfields'));
        } elseif ($filter != 'default') {
            $filter = $this->getFilterRepository()->oneRowFilter($filter, $this->entity);

            $rows = $this->fb->getRowQuery($filter->getConditions(), $this->entity);
        } else {
            $entityRepository = $this->em->getRepository(get_class($this->entity));

            $rows = $entityRepository->createQueryBuilder('a');
        }

        $rows = $this->sortRows($rows);

        $pagination = new Paginator($rows, $limit, $page);
        $this->pagination = $pagination;

        return $pagination;
    }

    /**
     * Sort rows
     *
     * If a specific sort parameter is given, order the results by that parameter
     * and the given direction.
     *
     * @param  QueryBuilder $rowQuery
     *
     * @return QueryBuilder
     */
    public function sortRows($rowQuery)
    {
        $query = $this->request->query;
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
     * Transform the columns to be used within the grid
     *
     * @param  array $columns
     * @return array
     */
    public function parseColumns($columns)
    {
        $data = array();

        foreach ($columns as $column) {
            if (isset($column['options']['label'])) {
                $label = $column['options']['label'];
            } else {
                $label = ucfirst(trim(preg_replace(
                    // (1) Replace special chars by spaces
                    // (2) Insert spaces between lower-case and upper-case
                    ['/[_\W]+/', '/([a-z])([A-Z])/'],
                    [' ', '$1 $2'],
                    $column['property']
                )));
            }
            $data[] = [
                'label' => $label,
                'property' => $column['property'],
                'type' => $column['type']
            ];
        }

        return $data;
    }

    /**
     * Transform the rows to be used within the grid
     *
     * @param  [array|Traversable] $rows
     * @return array
     */
    public function parseRows($rows)
    {
        if (!is_array($rows) && !$rows instanceof \Traversable) {
            throw new \InvalidArgumentException(
                'The grid items should be an array or a \Traversable.'
            );
        }
        $return = [];
        $accessor = PropertyAccess::getPropertyAccessor();

        foreach ($rows as $row) {
            $return[$row->getId()] = array_map(function ($column) use ($row, $accessor) {
                // in case of one-to-many or many-to-many relations, show
                // the count of related rows
                if (substr($column['property'], -strlen('.count')) === '.count') {
                    $explode = explode('.', $column['property']);
                    $explode[0] = 'get' . ucfirst($explode[0]);
                    $data['value'] = $row->$explode[0]()->$explode[1]();
                } else {
                    try {
                        $data['value'] = $accessor->getValue($row, $column['property']);
                    } catch (\Exception $e) {
                        $data['value'] = null;
                    }
                }

                if ($data['value'] instanceof \DateTime) {
                    $data['value'] = $data['value']->format('d-m-Y');
                } elseif (is_array($data['value'])) {
                    $data['value'] = json_encode($data['value']);
                }

                $data['id'] = $row->getId();
                $data['property'] = $column['property'];

                if (isset($column['type'])) {
                    $data['type'] = $column['type'];
                }

                return $data;
            }, $this->columns);
        }

        return $return;
    }

    /**
     * Sets the template to be used for the list view
     * Default value is listed as protected above.
     *
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Retrieves all filters and sets them as options for the filter selectors
     *
     * @param string $entity
     */
    public function setFilterSelectors()
    {
        $filterRepository = $this->getFilterRepository();

        $this->columnfilters = $filterRepository->columnFilters(get_class($this->entity));
        $this->rowfilters = $filterRepository->rowFilters(get_class($this->entity));

        return $this;
    }

    public function getFilterRepository()
    {
        return $this->em->getRepository('OpiferCrudBundle:CrudFilter');
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function getTemplate($method = 'GET')
    {
        if (!is_null($this->template)) {
            return $this->template;
        }

        // In case of an ajax call
        // if ($method == 'POST')
        //     return 'OpiferCrudBundle:DataGrid:table.html.twig';
        return 'OpiferCrudBundle:Crud:list.html.twig';
    }

    public function getColumnFilters()
    {
        return $this->columnfilters;
    }

    public function getRowFilters()
    {
        return $this->rowfilters;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function getPagination()
    {
        return $this->pagination;
    }

    public function getSelectedColumns()
    {
        return $this->selectedColumns;
    }

    public function getSelectedRows()
    {
        return $this->selectedRows;
    }
}
