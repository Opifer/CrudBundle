<?php

namespace Opifer\CrudBundle\Datagrid\Cell\Type;

use Opifer\CrudBundle\Datagrid\Column\Column;

class LabelCell extends AbstractCell
{
    /**
     * {@inheritDoc}
     */
    public function getData($row, Column $column, array $attributes)
    {
        $value = parent::getData($row, $column, $attributes);

        if (isset($attributes['map']) && count($attributes)) {
            $value = $attributes['map'][$value];
        } else {
            $value = ($value) ? 'True' : 'False';
        }

        return $value;
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
