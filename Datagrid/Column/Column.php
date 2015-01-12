<?php

namespace Opifer\CrudBundle\Datagrid\Column;

use Opifer\CrudBundle\Datagrid\Cell\Type\CellTypeInterface;

/**
 * Column
 *
 * Represents a column in a grid view
 */
class Column
{
    /** @var string */
    protected $property;

    /** @var label */
    protected $label;

    /** @var cellType */
    protected $cellType;

    /** @var \Closure */
    protected $closure;

    /** @var Array */
    protected $attributes;

    /** @var bool */
    protected $sortable;

    /**
     * Set property
     *
     * @param string $property
     */
    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Set label
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set cellType
     *
     * @param string $cellType
     */
    public function setCellType(CellTypeInterface $cellType)
    {
        $this->cellType = $cellType;

        return $this;
    }

    /**
     * Get cellType
     *
     * @return string
     */
    public function getCellType()
    {
        return $this->cellType;
    }

    /**
     * Set closure
     *
     * @param \Closure $closure
     */
    public function setClosure(\Closure $closure)
    {
        $this->closure = $closure;

        return $this;
    }

    /**
     * Get closure
     *
     * @return \Closure
     */
    public function getClosure()
    {
        return $this->closure;
    }

    /**
     * Set attributes
     *
     * @param \Closure $attributes
     */
    public function setAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get cell attributes
     *
     * @return array
     */
    public function getCellAttributes()
    {
        $attributes = (isset($this->attributes['cell']))
            ? $this->attributes['cell']
            : [];

        return $attributes;
    }

    /**
     * @return boolean
     */
    public function isSortable()
    {
        return $this->sortable;
    }

    /**
     * @param boolean $isSortable
     */
    public function setSortable($sortable)
    {
        $this->sortable = $sortable;

        return $this;
    }
}
