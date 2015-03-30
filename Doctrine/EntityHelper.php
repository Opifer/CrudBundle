<?php

namespace Opifer\CrudBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;

class EntityHelper
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Constructor.
     *
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Gets all own properties by entity.
     *
     * @param string|Object $entity
     *
     * @return array
     */
    public function getProperties($entity)
    {
        return $this->getMetaData($entity)->fieldMappings;
    }

    /**
     * Gets all related columns by entity.
     *
     * @param string|Object $entity
     *
     * @return array
     */
    public function getRelations($entity)
    {
        return $this->getMetaData($entity)->getAssociationMappings();
    }

    /**
     * Gets both the standard properties and the relations.
     *
     * @param string|Object $entity
     *
     * @return array
     */
    public function getAllProperties($entity)
    {
        return array_merge($this->getProperties($entity), $this->getRelations($entity));
    }

    /**
     * get Metadata from entity.
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
     * Get the Discriminator Map which defines the Single Table Inheritence mapping.
     *
     * @param string|Object $entity
     *
     * @return array
     */
    public function getDiscriminatorMap($entity)
    {
        return $this->getMetaData($entity)->discriminatorMap;
    }

    /**
     * @param object $entity
     */
    public function connectAddedRelations($entity)
    {
        $relations = $this->getRelations($entity);

        foreach ($relations as $key => $relation) {
            if ($relation['isOwningSide'] === false) {

                // Set getters and setters
                $getRelations = 'get'.ucfirst($relation['fieldName']);
                $setRelation  = 'set'.ucfirst($relation['mappedBy']);

                // Connect the added relations
                foreach ($entity->$getRelations() as $relationClass) {
                    $relationClass->$setRelation($entity);

                    $this->connectAddedRelations($relationClass);
                }
            }
        }

        $this->em->persist($entity);
        $this->em->flush();
    }

    /**
     * @param object $entity
     * @param object $currentEntity
     */
    public function disconnectRemovedRelations($entity, $currentEntity)
    {
        $currentRelations = $this->getRelations($currentEntity);

        // Set original relations, to be used after form's isValid method passed
        foreach ($currentRelations as $key => $relation) {
            if ($relation['isOwningSide'] === false) {

                // Set getters and setters
                $originalRelations[$key] = new ArrayCollection();
                $getRelations = 'get'.ucfirst($relation['fieldName']);

                foreach ($entity->$getRelations() as $relationEntity) {
                    $originalRelations[$key]->add($relationEntity);
                }
            }
        }

        $newRelations = $this->getRelations($entity);

        foreach ($newRelations as $key => $relation) {
            if ($relation['isOwningSide'] === false) {

                // Set getters and setters
                $getRelations = 'get'.ucfirst($relation['fieldName']);
                $setRelation  = 'set'.ucfirst($relation['mappedBy']);

                // Disconnect the removed relations
                foreach ($originalRelations[$key] as $relationEntity) {
                    if (false === $entity->$getRelations()->contains($relationEntity)) {
                        $relationEntity->$setRelation(null);

                        $em->persist($relationEntity);
                    }
                }
            }
        }

        $this->em->persist($entity);
        $this->em->flush();
    }
}
