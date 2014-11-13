<?php

namespace Opifer\CrudBundle\Datagrid\Cell\Type;

use Opifer\CrudBundle\Datagrid\Column\Column;

class IconCell extends AbstractCell
{
    /**
     * {@inheritDoc}
     */
    public function getData($row, Column $column, array $attributes)
    {
        $value = parent::getData($row, $column, $attributes);

        if (isset($attributes['map']) && count($attributes)) {
            if (isset($attributes['map'][$value])) {
                $value = $attributes['map'][$value];
            }
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getView()
    {
        return 'icon';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'icon';
    }
}
