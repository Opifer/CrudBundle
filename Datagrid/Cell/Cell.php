<?php

namespace Opifer\CrudBundle\Datagrid\Cell;

/**
 * Cell
 *
 * Represents a single cell inside a Row.
 */
class Cell
{
    /** @var string */
    protected $property;

    /** @var string */
    protected $type;

    /** @var string */
    protected $view;

    /** @var string */
    protected $value;

    /** @var string */
    protected $exportValue;

    /** @var Array */
    protected $attributes;

    /**
     * Set property
     *
     * @param string $property
     *
     * @return $this
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
     * Set type
     *
     * @param string $type
     *
     * @return $this
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
     * Set view
     *
     * @param string $view
     *
     * @return $this
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get view
     *
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set exportValue
     *
     * @param string $exportValue
     *
     * @return string
     */
    public function setExportValue($exportValue)
    {
        $this->exportValue = $exportValue;

        return $this;
    }

    /**
     * Get exportValue
     *
     * @return string
     */
    public function getExportValue()
    {
        return $this->exportValue;
    }

    /**
     * Set attributes
     *
     * @param array $attributes
     *
     * @return $this
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
}
