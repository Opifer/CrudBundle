<?php

namespace Opifer\CrudBundle\Datagrid\Row;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\CrudBundle\Datagrid\Cell\Cell;

/**
 * Row
 *
 * Holding all data necessary to create a single row inside the datagrid
 */
class Row
{
    /** @var integer */
    protected $id;

    /** @var string */
    protected $name;

    /** @var object */
    protected $object;

    /** @var ArrayCollection */
    protected $cells;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cells = new ArrayCollection();
    }

    /**
     * Set ID
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set object
     *
     * @param string $object
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return string
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Add a cell
     *
     * @param Cell $cell
     */
    public function addCell(Cell $cell)
    {
        $this->cells->add($cell);

        return $this;
    }

    /**
     * Get all cells
     *
     * @return ArrayCollection
     */
    public function getCells()
    {
        return $this->cells;
    }
}
