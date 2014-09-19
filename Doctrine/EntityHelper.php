<?php

namespace Opifer\CrudBundle\Doctrine;

use Doctrine\ORM\EntityManager;

class EntityHelper
{
    /**
     * @var  \Doctrine\ORM\EntityManager  $em
     */
    protected $em;

    /**
     * Constructor
     *
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Gets all own properties by entity
     *
     * @param  string|Object $entity
     * @return array
     */
    public function getProperties($entity)
    {
        return $this->getMetaData($entity)->fieldMappings;
    }

    /**
     * Gets all related columns by entity
     *
     * @param  string|Object $entity
     * @return array
     */
    public function getRelations($entity)
    {
        return $this->getMetaData($entity)->getAssociationMappings();
    }

    /**
     * Gets both the standard properties and the relations
     *
     * @param  string|Object $entity
     * @return array
     */
    public function getAllProperties($entity)
    {
        return array_merge($this->getProperties($entity), $this->getRelations($entity));
    }

    /**
     * get Metadata from entity
     *
     * @param string|Object $entity
     *
     * @return Doctrine\ORM\Mapping\ClassMetaData
     */
    public function getMetaData($entity)
    {
        if (is_object($entity)) {
            $entity = get_class($entity);
        }

        return $this->em->getClassMetadata($entity);
    }

    /**
     * Get the Discriminator Map which defines the Single Table Inheritence mapping
     *
     * @param  string|Object $entity
     * @return array
     */
    public function getDiscriminatorMap($entity)
    {
        return $this->getMetaData($entity)->discriminatorMap;
    }
}
