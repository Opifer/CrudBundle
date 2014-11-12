<?php

namespace Opifer\CrudBundle\Datagrid\Cell\Type;

use Opifer\CrudBundle\Datagrid\Column\Column;

class LabelCell extends PropertyCell
{
    /**
     * {@inheritDoc}
     */
    public function getData($row, Column $column)
    {
        $value = parent::getData($row, $column);

        return ($value == 1) ? 'Yes' : 'No';
    }

    /**
     * {@inheritDoc}
     */
    public function getView()
    {
        return 'label';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'label';
    }
}
