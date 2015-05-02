<?php

namespace Opifer\CrudBundle\Annotation;

/**
 * Reading the Form annotations
 * Defined in Opifer\CrudBundle\Annotation\Form
 */
class FormAnnotationReader extends AbstractAnnotationReader
{

    protected $annotationClass = 'Opifer\\CrudBundle\\Annotation\\Form';

    /**
     * Return type for class if set, otherwise return false.
     *
     * @param  Object $entity
     * @return string
     */
    public function getClassType($entity)
    {
        $reflectionClass = new \ReflectionClass($entity);
        $classAnnotation = $this->reader->getClassAnnotation($reflectionClass, $this->annotationClass);

        if (!is_null($classAnnotation) && $classAnnotation->type) {
            return $classAnnotation->type;
        }

        return false;
    }

    /**
     * Return type for property if set, otherwise return false.
     *
     * @param  Object $entity
     * @param  string $property
     * @return string
     */
    public function getPropertyType($entity, $property)
    {
        $reflectionProperty = new \ReflectionProperty($entity, $property);
        $propertyAnnotation = $this->reader->getPropertyAnnotation($reflectionProperty, $this->annotationClass);

        if (!is_null($propertyAnnotation) && $propertyAnnotation->type) {
            return $propertyAnnotation->type;
        }

        return false;
    }

    /**
     * Get all entity properties with the 'editable' annotation set to true
     *
     * @param  Object $entity
     * @return array
     */
    public function getEditableProperties($entity)
    {
        $properties = $this->get($entity, 'editable');

        $class = new \ReflectionClass($entity);

        while ($parent = $class->getParentClass()) {
            $parentProperties = $this->get($parent->getName(), 'editable');
            $properties = array_merge($parentProperties, $properties);
            $class = $parent;
        }

        return $properties;
    }

    /**
     * Check whether the given property is editable
     *
     * @param  Object  $entity
     * @param  string  $property
     * @return boolean
     */
    public function isEditable($entity, $property)
    {
        return $this->is($entity, $property, 'editable');
    }

}
