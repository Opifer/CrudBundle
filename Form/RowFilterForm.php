<?php

namespace Opifer\CrudBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RowFilterForm extends AbstractType
{
    protected $entity;

    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('conditions', 'ruleeditor', [
                'provider' => 'crud',
                'context' => $this->entity,
            ])
            ->add('Save filter', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Opifer\CrudBundle\Entity\RowFilter',
        ));
    }

    public function getName()
    {
        return 'rowfilter';
    }
}
