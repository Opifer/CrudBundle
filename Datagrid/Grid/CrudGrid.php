<?php

namespace Opifer\CrudBundle\Datagrid\Grid;

use Opifer\CrudBundle\Datagrid\DatagridBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CrudGrid extends AbstractGrid
{
    protected $slug;

    /**
     * Constructor
     *
     * @param string $slug
     */
    public function __construct($slug)
    {
        $this->slug = $slug;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'batch_actions' => [
                'delete' => ['opifer.crud.batch.delete', ['slug' => $this->slug]]
            ],
            'actions' => [
                'edit'   => ['template' => 'OpiferCrudBundle:Datagrid:action_edit.html.twig'],
                'delete' => ['template' => 'OpiferCrudBundle:Datagrid:action_delete.html.twig'],
            ]
        ]);
    }
}
