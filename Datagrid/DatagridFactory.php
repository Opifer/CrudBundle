<?php

namespace Opifer\CrudBundle\Datagrid;

use Opifer\CrudBundle\Datagrid\Grid\GridInterface;

class DatagridFactory
{
    /** @var DatagridBuilderInterface $builder */
    protected $builder;

    /**
     * Constructor
     *
     * @param DatagridBuilderInterface $builder
     */
    public function __construct(DatagridBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Create
     *
     * @param  GridInterface $grid
     * @param  object        $data
     *
     * @return Datagrid
     */
    public function create(GridInterface $grid, $data)
    {
        $builder = $this->builder->create($data);

        $grid->buildGrid($builder);

        return $builder->build();
    }
}
