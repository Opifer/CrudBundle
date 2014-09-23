<?php

namespace Opifer\CrudBundle\Datagrid\Column;

class Column
{
    protected $property;

    protected $label;

    protected $type;

    public function setProperty($property)
    {
        $this->property = $property;

        return $this;
    }

    public function getProperty()
    {
        return $this->property;
    }

    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }
}
