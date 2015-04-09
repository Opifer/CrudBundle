<?php

namespace Opifer\CrudBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Opifer\CrudBundle\Annotation\FormAnnotationReader;
use Opifer\CrudBundle\Doctrine\EntityHelper;
use Opifer\CrudBundle\Transformer\DoctrineTypeTransformer;

/**
 * Crud relation type
 *
 * @author Rick van Laarhoven <r.vanlaarhoven@opifer.nl>
 */
class CrudRelationType extends AbstractType
{
    /**
     * @var EntityHelper
     */
    protected $entityHelper;

    /**
     * The complete class namespace
     *
     * @var  string
     */
    protected $entity;

    /**
     * @var FormAnnotationReader
     */
    protected $annotationReader;

    /**
     * Constructor
     *
     * @param EntityHelper         $entityHelper
     * @param string               $object
     * @param FormAnnotationReader $annotationReader
     */
    public function __construct(EntityHelper $entityHelper, $object, FormAnnotationReader $annotationReader)
    {
        $this->entityHelper = $entityHelper;
        $this->object = $object;
        $this->annotationReader = $annotationReader;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addOwnProperties($builder);
        $this->addRelations($builder);
    }

    /**
     * Add own properties
     *
     * @param FormBuilderInterface $builder
     */
    public function addOwnProperties(FormBuilderInterface $builder)
    {
        $transformer = new DoctrineTypeTransformer();
        $properties = $this->entityHelper->getProperties($this->object);

        foreach ($properties as $property) {
            if (!$this->isAllowed($property['fieldName'])) {
                continue;
            }

            $type = $transformer->transform($property['type']);

            if ($propertyType = $this->annotationReader->getPropertyType($this->object, $property['fieldName'])) {
                $builder->add($property['fieldName'], $propertyType);
            } elseif ('bootstrap_collection' == $type) {
                $builder->add($property['fieldName'], $type, [
                    'allow_add'    => true,
                    'allow_delete' => true,
                ]);
            } else {
                $builder->add($property['fieldName'], $type);
            }
        }
    }

    /**
     * Add relations to the form
     *
     * @param FormBuilderInterface $builder
     */
    public function addRelations(FormBuilderInterface $builder)
    {
        $relations = $this->entityHelper->getRelations($this->object);

        foreach ($relations as $key => $relation) {
            if (!$this->isAllowed($relation['fieldName'])) {
                continue;
            }

            if ($relation['isOwningSide'] === false) {
                $builder->add($relation['fieldName'], 'collapsible_collection', [
                    'allow_add' => true,
                    'allow_delete' => true,
                    'type'         => new CrudRelationType($this->entityHelper, $relation['targetEntity'], $this->annotationReader),
                    'by_reference' => false
                ]);
            }
        }
    }

    /**
     * Check if the property is allowed
     *
     * @param  string  $property
     *
     * @return boolean
     */
    public function isAllowed($property)
    {
        if (count($this->getAllowedProperties()) &&
            !in_array($property, $this->getAllowedProperties())) {
            return false;
        }

        return true;
    }

    /**
     * Get the allowed properties from the object
     *
     * @return array
     */
    public function getAllowedProperties()
    {
        return $this->annotationReader->getEditableProperties($this->object);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->object,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'crud_relation';
    }
}
