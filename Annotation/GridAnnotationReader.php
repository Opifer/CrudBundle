<?php

namespace Opifer\CrudBundle\Annotation;

/**
 * Reading the Grid annotations
 * Defined in Opifer\CrudBundle\Annotation\Grid
 */
class GridAnnotationReader extends AbstractAnnotationReader
{
    protected $annotationClass = 'Opifer\\CrudBundle\\Annotation\\Grid';

    /**
     * Get all entity properties with the 'listable' annotation set to true
     *
     * @param  Object $entity
     * @return array
     */
    public function getListableProperties($entity)
    {
        return $this->get($entity, 'listable');
    }

    /**
     * Gets all Grid annotations
     *
     * @param  [type] $entity [description]
     * @return [type] [description]
     */
    public function all($entity)
    {
        $reflectionClass = new \ReflectionClass($entity);
        $properties = $reflectionClass->getProperties();

        $class = new \ReflectionClass($entity);

        while ($parent = $class->getParentClass()) {
            $parentProperties = $parent->getProperties();
            $properties = array_merge($parentProperties, $properties);
            $class = $parent;
        }

        $return = array();
        foreach ($properties as $reflectionProperty) {
            $propertyAnnotation = $this->reader->getPropertyAnnotation($reflectionProperty, $this->annotationClass);
            if (!is_null($propertyAnnotation) && $propertyAnnotation->listable) {
                $return[] = array(
                    'property' => $reflectionProperty->name,
                    'type' => $propertyAnnotation->type
                );
            }
        }

        return $return;
    }
}
