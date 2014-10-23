<?php

namespace Opifer\CrudBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ListViewType extends AbstractType
{
    /** @var object $entity */
    protected $entity;

    protected $columns;

    /**
     * Constructor
     *
     * @param object $entity
     */
    public function __construct($entity, array $columns)
    {
        $this->entity = $entity;
        $this->columns = $columns;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $columns = [];
        foreach ($this->columns as $column) {
            // Temporarily disable relations
            if (isset($column['joinColumns'])) {
                continue;
            }
            $columns[$column['fieldName']] = $column['fieldName'];
        }

        $builder
            ->add('name', 'text', [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Save view as'
                ]
            ])
            ->add('conditions', 'ruleeditor', [
                'label'    => 'Filters',
                'provider' => (method_exists($this->entity, 'getRuleProvider')) ? $this->entity->getRuleProvider() : 'entity',
                'context'  => get_class($this->entity),
                'required' => false
            ])
            // ->add('columns', 'choice', [
            //     'choices' => [$columns],
            //     'multiple' => true,
            //     'expanded' => true,
            //     'required' => true
            // ])
            ->add('apply', 'submit', ['label' => 'Apply'])
            ->add('save', 'submit', ['label' => 'Save'])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Opifer\CrudBundle\Entity\ListView',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'listview';
    }
}
