<?php

namespace Opifer\CrudBundle\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Our abstract annotation reader
 *
 * Providing some default functionality used in the child annotation readers
 */
abstract class AbstractAnnotationReader
{
    protected $reader;
    protected $annotationClass;

    public function __construct()
    {
        $this->reader = new AnnotationReader();
    }

    /**
     * Get annotations by entity and annotation name
     *
     * @param  Object $entity
     * @param  string $annotation
     * @return array
     */
    public function get($entity, $annotation)
    {
        $reflectionClass = new \ReflectionClass($entity);
        $properties = $reflectionClass->getProperties();

        $return = array();
        foreach ($properties as $reflectionProperty) {
            $propertyAnnotation = $this->reader->getPropertyAnnotation($reflectionProperty, $this->annotationClass);
            if (!is_null($propertyAnnotation) && $propertyAnnotation->$annotation) {
                $return[] = $reflectionProperty->name;
            }
        }

        return $return;
    }

    /**
     * Check whether the given property & annotation combination returns either
     * true / false
     *
     * @param  Object  $entity
     * @param  string  $property
     * @param  string  $annotation
     * @return boolean
     */
    public function is($entity, $property, $annotation)
    {
        $reflectionProperty = new \ReflectionProperty($entity, $property);
        $propertyAnnotation = $this->reader->getPropertyAnnotation($reflectionProperty, $this->annotationClass);

        return $propertyAnnotation->$annotation;
    }
}
