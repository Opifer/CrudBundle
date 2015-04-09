<?php

namespace Opifer\CrudBundle\Manager;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Opifer\CrudBundle\Doctrine\EntityHelper;

class RelationManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EntityHelper
     */
    private $entityHelper;

    /**
     * @param EntityManager $em
     * @param EntityHelper $entityHelper
     */
    function __construct(EntityManager $em, EntityHelper $entityHelper)
    {
        $this->em = $em;
        $this->entityHelper = $entityHelper;
    }

    /**
     * retrieves all original relations
     *
     * @param $originalRelations
     * @param $relations
     * @param $entity
     * @return array
     */
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
     * Builds the relations properly so all relations can get saved properly
     *
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
                $getBackReference = 'get' . ucfirst($relation['mappedBy']);

                // Connect the added relations
                foreach ($entity->$getRelations() as $relationClass) {
                    if ($relationClass->$getBackReference() instanceof Collection) {
                        if (!$relationClass->$getBackReference()->contains($entity)) {
                            $relationClass->$getBackReference()->add($entity);
                        }
                    } else {
                        $relationClass->$setRelation($entity);
                    }

                    $relationRelations = $this->entityHelper->getRelations($relationClass);
                    $this->setRelations($relationRelations, $originalRelations[$key]['relations'], $relationClass);
                }

                // Disconnect the removed relations
                if(array_key_exists($key, $originalRelations)) {
                    foreach ($originalRelations[$key]['entities'] as $relationEntity) {
                        if (false === $entity->$getRelations()->contains($relationEntity)) {
                            if ($relationEntity->$getBackReference() instanceof Collection) {
                                $relationEntity->$getBackReference()->removeElement($relationEntity);
                            } else {
                                $relationEntity->$setRelation(null);
                            }

                            $this->em->persist($relationEntity);
                        }
                    }
                }
            }

        }
    }
}