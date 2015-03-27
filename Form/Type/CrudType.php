<?php

namespace Opifer\CrudBundle\Form\Type;

use Genemu\Bundle\FormBundle\Form\JQuery\Type\Select2Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Opifer\CrudBundle\Annotation\FormAnnotationReader;
use Opifer\CrudBundle\Doctrine\EntityHelper;
use Opifer\CrudBundle\Form\DataTransformer\ArrayToJsonTransformer;
use Opifer\CrudBundle\Transformer\DoctrineTypeTransformer;

class CrudType extends AbstractType
{
    /** @var EntityHelper */
    protected $entityHelper;

    /** @var FormAnnotationReader */
    protected $annotationReader;

    /**
     * Constructor
     *
     * @param EntityHelper         $entityHelper
     * @param FormAnnotationReader $annotationReader
     * @param string               $valuesetClass
     */
    public function __construct(EntityHelper $entityHelper, FormAnnotationReader $annotationReader, $valuesetClass)
    {
        $this->valuesetClass = $valuesetClass;
        $this->annotationReader = $annotationReader;
        $this->entityHelper = $entityHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $allowedProperties = $this->annotationReader->getEditableProperties($options['data']);
        $transformer = new DoctrineTypeTransformer();

        // Get the standard property form fields
        foreach ($this->entityHelper->getProperties($options['data']) as $property) {
            if (count($allowedProperties) && !in_array($property['fieldName'], $allowedProperties)) {
                continue;
            }

            if ($propertyType = $this->annotationReader->getPropertyType($options['data'], $property['fieldName'])) {
                $builder->add($property['fieldName'], $propertyType);
            } elseif ($property['type'] == 'json_array') {
                $builder->add(
                    $builder->create($property['fieldName'], 'textarea')
                        ->addModelTransformer(new ArrayToJsonTransformer()
                    )
                );
            } elseif ($property['type'] == 'simple_array') {
                $builder->add(
                    $builder->create($property['fieldName'], 'bootstrap_collection', [
                        'allow_add'          => true,
                        'allow_delete'       => true,
                        'add_button_text'    => 'Add',
                        'delete_button_text' => 'Delete',
                        'sub_widget_col'     => 8,
                        'button_col'         => 4
                    ])
                );
            } else {
                $builder->add($property['fieldName'], $transformer->transform($property['type']));
            }
        }
        
        // Get the relations' form fields
        foreach ($this->entityHelper->getRelations($options['data']) as $key => $relation) {
            if (count($allowedProperties) && !in_array($relation['fieldName'], $allowedProperties)) {
                continue;
            }
           
            if ($relation['isOwningSide'] === false) {
                $builder->add($relation['fieldName'], 'bootstrap_collection', [
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'type'         => new CrudRelationType($this->entityHelper, $relation['targetEntity'], $this->annotationReader),
                    'by_reference' => false
                ]);
            } elseif ($relation['targetEntity'] == $this->valuesetClass) {
                $builder->add($relation['fieldName'], 'opifer_valueset');
            }else {
                $builder->add($relation['fieldName'], new Select2Type('entity'), [
                    'class'       => $relation['targetEntity'],
                    'property'    => 'name',
                    'empty_value' => '(empty)',
                    'empty_data'  => null,
                ]);
            }
        }

        $builder->add('save', 'submit', ['label' => 'Save']);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'crud_form';
    }
}
