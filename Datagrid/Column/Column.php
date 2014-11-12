<?php

namespace Opifer\CrudBundle\Datagrid\Column;

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

    /** @var type */
    protected $type;

    /** @var \Closure */
    protected $closure;

    /** @var Array */
    protected $attributes;

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
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
    public function setAttributes(array $attributes)
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
}
