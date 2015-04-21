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
                $originalRelations[$key] = [];

                $getRelations = 'get' . ucfirst($relation['fieldName']);

                foreach ($entity->$getRelations() as $relationEntity) {
                    $entRelationArray['entity'] = $relationEntity;
                    $entRelationArray['relations'] = [];


                    $relationRelations = $this->entityHelper->getRelations($relationEntity);
                    $entRelationArray['relations'] = $this->originalRelations($entRelationArray['relations'], $relationRelations, $relationEntity);

                    $originalRelations[$key][$relationEntity->getId()] = $entRelationArray;
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
                foreach ($entity->$getRelations() as $relationEntity) {
                    if ($relationEntity->$getBackReference() instanceof Collection) {
                        if (!$relationEntity->$getBackReference()->contains($entity)) {
                            $relationEntity->$getBackReference()->add($entity);
                        }
                    } else {
                        $relationEntity->$setRelation($entity);
                    }

                    $relationRelations = $this->entityHelper->getRelations($relationEntity);

                    $originalRelationsForEntity = count($originalRelations) > 0 &&
                        array_key_exists($relationEntity->getId(), $originalRelations[$key]) ?
                        $originalRelations[$key][$relationEntity->getId()]['relations'] :
                        [];
                    $this->setRelations($relationRelations, $originalRelationsForEntity, $relationEntity);
                }

                // Disconnect the removed relations
                if(array_key_exists($key, $originalRelations)) {
                    foreach ($originalRelations[$key] as $relationEntityArray) {
                        if (false === $entity->$getRelations()->contains($relationEntityArray['entity'])) {
                            if ($relationEntityArray['entity']->$getBackReference() instanceof Collection) {
                                $relationEntityArray['entity']->$getBackReference()->removeElement($relationEntityArray['entity']);
                            } else {
                                $relationEntityArray['entity']->$setRelation(null);
                            }

                            $this->em->persist($relationEntityArray['entity']);
                        }
                    }
                }
            }

        }
    }
}