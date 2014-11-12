<?php

namespace Opifer\CrudBundle\Datagrid\Cell\Type;

use Opifer\CrudBundle\Datagrid\Column\Column;

class DateTimeCell extends PropertyCell
{
    protected $format;

    public function __construct($format = 'd-m-Y')
    {
        $this->format = $format;
    }

    /**
     * {@inheritDoc}
     */
    public function getData($row, Column $column)
    {
        $value = parent::getData($row, $column);

        return $value->format($this->format);
    }

    /**
     * {@inheritDoc}
     */
    public function getView()
    {
        return 'text';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'datetime';
    }
}
