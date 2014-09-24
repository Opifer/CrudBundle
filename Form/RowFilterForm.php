<?php

namespace Opifer\CrudBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RowFilterForm extends AbstractType
{
    /** @var object $entity */
    protected $entity;

    protected $action;

    /**
     * Constructor
     *
     * @param object $entity
     */
    public function __construct($entity, $action = null)
    {
        $this->entity = $entity;
        $this->action = $action;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('conditions', 'ruleeditor', [
                'label' => 'Filters',
                'provider' => $this->entity->getRuleProvider(),
                'context' => $this->entity,
            ])
            ->add('Apply', 'submit')
        ;

        // if (null !== $this->action) {
        //     $builder->setAction($this->action);
        // }
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Opifer\CrudBundle\Entity\RowFilter',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'rowfilter';
    }
}
