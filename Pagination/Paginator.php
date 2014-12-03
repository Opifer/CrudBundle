<?php

namespace Opifer\CrudBundle\Pagination;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Paginator
 *
 * Our custom pagination layer to ease a possible switch between paginators
 *
 * Usage:
 * ------
 *
 * $pager = new Pager($qb, $limit, $page);
 *
 * Because the pager implements \IteratorAggregate, we can loop over
 * the results like a normal array.
 *
 * @author Rick van Laarhoven <r.vanlaarhoven@opifer.nl>
 */
class Paginator extends Pagerfanta implements \Countable, \IteratorAggregate
{
    protected $range = 2;

    /**
     * Constructor
     *
     * @param mixed   $items
     * @param integer $page
     * @param integer $limit
     */
    public function __construct($items, $limit = 25, $page = 1)
    {
        parent::__construct($this->createAdapterFromInstance($items));

        $this->setMaxPerPage($limit);
        $this->setCurrentPage($page);
    }

    /**
     * Pagerfanta works with pagination adapters, so we transform the passed $items
     * to the appropriate adapter.
     *
     * @param mixed $items
     *
     * @return \Pagerfanta\Adapter\AdapterInterface
     */
    protected function createAdapterFromInstance($items)
    {
        if ($items instanceof \Doctrine\ORM\QueryBuilder) {
            $adapter = new DoctrineORMAdapter($items);
        } else {
            throw new \Exception('This pager currently only supports Doctrine\'s QueryBuilder instances');
        }

        return $adapter;
    }

    /**
     * Get the total amount of pages
     *
     * @return  integer
     */
    public function getTotalPages()
    {
        return $this->getNbPages();
    }

    /**
     * Get the total amount of results
     *
     * @return integer
     */
    public function getTotalResults()
    {
        return $this->getNbResults();
    }

    /**
     * Set the page range
     *
     * @param  integer $range
     *
     * @return Paginator
     */
    public function setRange($range)
    {
        $this->range = $range;

        return $this;
    }

    /**
     * Get first page in range
     *
     * @return integer
     */
    public function getFirstInRange()
    {
        $start = $this->getCurrentPage() - $this->range;
        $start = ($start > 1) ? $start : 1;

        return $start;
    }

    /**
     * Get last page in range
     *
     * @return integer
     */
    public function getLastInRange()
    {
        $last = $this->getCurrentPage() + $this->range;
        $last = ($last < $this->getTotalPages()) ? $last : $this->getTotalPages();

        return $last;
    }
}
