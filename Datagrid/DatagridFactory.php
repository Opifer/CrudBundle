<?php

namespace Opifer\CrudBundle\Datagrid;

use Opifer\CrudBundle\Datagrid\GridType\GridTypeInterface;

class DatagridFactory
{
    protected $builder;

    public function __construct(DatagridBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function create(GridTypeInterface $type, $data)
    {
        $builder = $this->builder->create($data);

        $type->buildGrid($builder);

        return $builder->build();
    }
}
