<?php

namespace Opifer\CrudBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Opifer\CrudBundle\Annotation\FormAnnotationReader;
use Opifer\CrudBundle\Doctrine\EntityHelper;
use Opifer\CrudBundle\Transformer\DoctrineTypeTransformer;

class CrudRelationType extends AbstractType
{
    /**
     * @var  \Opifer\CrudBundle\Doctrine\EntityHelper
     */
    protected $entityHelper;

    /**
     * The complete class namespace
     *
     * @var  string
     */
    protected $entity;

    /**
     * @var  \Opifer\CrudBundle\Annotation\FormAnnotationReader
     */
    protected $annotationReader;

    /**
     * Constructor
     *
     * @param \Opifer\CrudBundle\Doctrine\EntityHelper $entityHelper
     * @param string                                   $entity
     */
    public function __construct(EntityHelper $entityHelper, $entity, FormAnnotationReader $annotationReader)
    {
        $this->entityHelper = $entityHelper;
        $this->entity = $entity;
        $this->annotationReader = $annotationReader;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $allowedProperties = $this->annotationReader->getEditableProperties($this->entity);
        $transformer = new DoctrineTypeTransformer();

        foreach ($this->entityHelper->getProperties($this->entity) as $property) {
            if (count($allowedProperties) && !in_array($property['fieldName'], $allowedProperties)) {
                continue;
            }

            if (!in_array($property['fieldName'], ['id', 'slug', 'createdAt', 'updatedAt', 'deletedAt'])) {
                if ($propertyType = $this->annotationReader->getPropertyType($this->entity, $property['fieldName'])) {
                    $builder->add($property['fieldName'], $propertyType);
                } elseif ('bootstrap_collection' == $type = $transformer->transform($property['type'])) {
                    $builder->add($property['fieldName'], $type, [
                        'allow_add' => true,
                        'allow_delete' => true,
                    ]);
                } else {
                    $builder->add($property['fieldName'], $type);
                }
            }
        }

        foreach ($this->entityHelper->getRelations($this->entity) as $key => $relation) {
            if (count($allowedProperties) && !in_array($relation['fieldName'], $allowedProperties)) {
                continue;
            }

            if ($relation['isOwningSide'] === false) {
                $builder->add('Edit ' . $relation['fieldName'], 'button', [
                    'attr' => ['class' => 'btn btn-primary']
                ]);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->entity,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'crud_collection';
    }
}
