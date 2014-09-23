<?php

namespace Opifer\CrudBundle\Datagrid;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;

/**
 * Filter Query Builder
 *
 * This class builds the filter query from a given array or json string.
 *
 * It depends greatly on Doctrine's QueryBuilder, so
 * the syntax should be pretty much the same.
 * @see http://docs.doctrine-project.org/en/latest/reference/query-builder.html
 */
class FilterQueryBuilder
{
    protected $em;
    protected $qb;
    protected $parameters = [];

    /**
     * Filter constructor
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->qb = new QueryBuilder($this->em);
    }

    /**
     * Generates the filter query
     * An example of an array which could be passed as the filter parameter:
     *
     * $filter = [
     *     'entity' => ['Acme\DemoBundle\Entity\Page', 'p'],
     *     'join' => [
     *         'innerJoin' => ['p.category', 'c']
     *     ],
     *     'where' => [
     *         'where' => ['p.id', 'gt', 1],
     *         'andWhere' => ['c.id', 'gt', 1]
     *     ],
     *     'orderBy' => [
     *         ['p.title', 'ASC']
     *     ],
     *     'limit' => 10
     * ];
     *
     * @param  array|string $filter
     * @return array
     */
    public function getBuilder($filter)
    {
        if (!is_array($filter)) {
            try {
                $filter = json_decode($filter, true);
            } catch (\ErrorException $e) {
                throw new \Exception('The filter should be either an array or valid json string');
            }
        }

        $this->init($filter['entity'])
             ->join($filter['join'])
             ->where($filter['where'])
             //->orderBy($filter['orderBy'])
             ->setParameters()
             ->setLimit($filter['limit'])
        ;

        return $this->qb;
    }

    public function getResults($filter)
    {
        return $this->getQuery($filter)->getResult();
    }

    /**
     * Initialize the query by setting a select() and from() to the query
     *
     * @param  array  $entity
     * @return Filter
     */
    public function init($entity)
    {
        $this->qb->select($entity[1])->from($entity[0], $entity[1]);

        return $this;
    }

    /**
     * Adds joined tables to the qb
     *
     * Allowed joins: innerJoin, leftJoin
     *
     * @param  array  $join
     * @return Filter
     */
    public function join($joins)
    {
        foreach ($joins as $key => $join) {
            if (!in_array($key, ['innerJoin','leftJoin']))
                throw new \Exception('Make sure to use innerJoin or leftJoin as joins');

            $this->qb->$key($join[0], $join[1]);
        }

        return $this;
    }

    /**
     * Adds where|andWhere|orWhere clauses to the qb
     *
     * parameters are being retrieved from the 3rd array element, to be added to
     * the setParameters() method later to prevent SQL injection attacks.
     *
     * @param  array  $wheres
     * @return Filter
     */
    public function where($wheres)
    {
        $i = 1;
        foreach ($wheres as $key => $where) {
            $this->qb->$key(
                $this->qb->expr()->$where[1](
                    $where[0],
                    '?' . $i
                )
            );
            if (preg_match('(like|notLike)', $where[1]))
                $where[2] = '%' . $where[2] . '%';

            $this->parameters[$i] = $where[2];
            $i++;
        }

        return $this;
    }

    /**
     * Adds an order to the qb
     *
     * @param  array  $order
     * @return Filter
     */
    public function orderBy($order)
    {
        if (isset($order[0])) {
            if ($order[0][1]) {
                $this->qb->orderBy($order[0][0], $order[0][1]);
            } else {
                // Use a default order direction when it's not defined
                $this->qb->orderBy($order[0][0], 'ASC');
            }
        }

        return $this;
    }

    /**
     * Set the parameters (mostly parsed from the where method) to the qb
     *
     * @return Filter
     */
    public function setParameters()
    {
        if ($this->parameters) {
            $this->qb->setParameters($this->parameters);
        }

        return $this;
    }

    /**
     * Set a max results limit to the query
     *
     * @param int $limit
     */
    public function setLimit($limit)
    {
        if (isset($limit) && (int) $limit) {
            $this->qb->setMaxResults($limit);
        }

        return $this;
    }
}
