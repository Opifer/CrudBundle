<?php

namespace Opifer\CrudBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CrudFilterRepository extends EntityRepository
{
    /**
     * One row filter
     *
     * @param string $slug
     * @param string $entity
     *
     * @return Filter
     */
    public function oneRowFilter($slug, $entity)
    {
        return $this->oneFilter('RowFilter', $slug, $entity);
    }

    /**
     * One column filter
     *
     * @param string $slug
     * @param string $entity
     *
     * @return Filter
     */
    public function oneColumnFilter($slug, $entity)
    {
        return $this->oneFilter('ColumnFilter', $slug, $entity);
    }

    /**
     * One filter
     *
     * @param string $type
     * @param string $slug
     * @param string $entity
     *
     * @return Filter
     */
    public function oneFilter($type, $slug, $entity)
    {
        $query = $this->createQueryBuilder('f')
            ->where('f INSTANCE OF OpiferCrudBundle:'.$type)
            ->andWhere('f.entity = :entity')
            ->andWhere('f.slug = :slug')
            ->setParameters([
                'entity' => $entity,
                'slug' => $slug
            ])
            ->getQuery()
        ;

        return $query->getSingleResult();
    }

    /**
     * Row filters
     *
     * @param string $entity
     *
     * @return \Doctrine\ORM\Collections\ArrayCollection
     */
    public function rowFilters($entity)
    {
        return $this->filters('RowFilter', $entity);
    }

    /**
     * Column Filters
     *
     * @param string $entity
     *
     * @return \Doctrine\ORM\Collections\ArrayCollection
     */
    public function columnFilters($entity)
    {
        return $this->filters('ColumnFilter', $entity);
    }

    /**
     * Filters
     *
     * @param string $type
     * @param string $entity
     *
     * @return \Doctrine\ORM\Collections\ArrayCollection
     */
    public function filters($type, $entity)
    {
        $query = $this->createQueryBuilder('f')
            ->where('f INSTANCE OF OpiferCrudBundle:'.$type)
            ->andWhere('f.entity = :entity')
            ->setParameter('entity', $entity)
            ->orderBy('f.name', 'ASC')
            ->getQuery()
        ;

        return $query->getResult();
    }
}
