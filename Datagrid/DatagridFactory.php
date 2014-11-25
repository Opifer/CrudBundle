<?php

namespace Opifer\CrudBundle\Datagrid;

use Opifer\CrudBundle\Datagrid\Grid\GridInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
        $datagrid = $builder->build();

        $this->handleOptions($grid, $datagrid);

        return $datagrid;
    }

    /**
     * Handle options
     *
     * @param  GridInterface $grid
     * @param  Datagrid      $datagrid
     *
     * @return void
     */
    protected function handleOptions(GridInterface $grid, Datagrid $datagrid)
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);

        $grid->configureOptions($resolver);

        $datagrid->setOptions($resolver->resolve());
    }

    /**
     * Configure options
     *
     * @param  OptionsResolverInterface $resolver
     *
     * @return void
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'batch_actions' => [],
            'actions' => []
        ]);

        $resolver->setRequired([
            'batch_actions'
        ]);

        $resolver->setAllowedTypes([
            'batch_actions' => 'array',
            'actions' => 'array'
        ]);
    }
}
