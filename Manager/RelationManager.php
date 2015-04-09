<?php

namespace Opifer\CrudBundle\Manager;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Opifer\CrudBundle\Doctrine\EntityHelper;

class RelationManager {
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EntityHelper
     */
    private $entityHelper;

    function __construct(EntityManager $em, EntityHelper $entityHelper)
    {
        $this->em = $em;
        $this->entityHelper = $entityHelper;
    }

    public function originalRelations($originalRelations, $relations, $entity)
    {
        foreach ($relations as $key => $relation) {
            if ($relation['isOwningSide'] === false) {
                $originalRelations[$key]['entities'] = new ArrayCollection();
                $originalRelations[$key]['relations'] = [];

                $getRelations = 'get' . ucfirst($relation['fieldName']);

                foreach ($entity->$getRelations() as $relationEntity) {
                    $originalRelations[$key]['entities']->add($relationEntity);

                    $relationRelations = $this->entityHelper->getRelations($relationEntity);
                    $originalRelations[$key]['relations'] = $this->originalRelations($originalRelations[$key]['relations'], $relationRelations, $relationEntity);
                }


            }
        }

        return $originalRelations;
    }

    /**
     * @param $relations
     * @param $originalRelations
     * @param $entity
     */
    public function setRelations($relations, $originalRelations, $entity)
    {
        foreach ($relations as $key => $relation) {
            if ($relation['isOwningSide'] === false) {
                // Set getters and setters
                $getRelations = 'get' . ucfirst($relation['fieldName']);
                $setRelation = 'set' . ucfirst($relation['mappedBy']);

                // Connect the added relations
                foreach ($entity->$getRelations() as $relationClass) {
                    $relationClass->$setRelation($entity);

                    $relationRelations = $this->entityHelper->getRelations($relationClass);
                    $this->setRelations($relationRelations, $originalRelations[$key]['relations'], $relationClass);
                }

                // Disconnect the removed relations
                if(array_key_exists($key, $originalRelations)) {
                    foreach ($originalRelations[$key]['entities'] as $relationEntity) {
                        if (false === $entity->$getRelations()->contains($relationEntity)) {
                            $relationEntity->$setRelation(null);

                            $this->em->persist($relationEntity);
                        }
                    }
                }
            }

        }
    }
}