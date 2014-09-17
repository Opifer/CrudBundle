<?php

namespace Opifer\CrudBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ColumnFilterForm extends AbstractType
{
    protected $columns;

    public function __construct($columns)
    {
        $this->columns = $columns;
    }

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
            ->add('name')
            ->add('columns', 'choice', [
                'choices' => [$columns],
                'multiple' => true,
                'expanded' => true,
                'required' => true
            ])
            ->add('Save filter', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Opifer\CrudBundle\Entity\CrudFilter',
        ));
    }

    public function getName()
    {
        return 'columnfilter';
    }
}
