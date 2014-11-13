<?php

namespace Opifer\CrudBundle\Datagrid\Cell\Type;

use Opifer\CrudBundle\Datagrid\Column\Column;

class CountCell extends AbstractCell
{
    /**
     * {@inheritDoc}
     */
    public function getData($row, Column $column, array $attributes)
    {
        $value = parent::getData($row, $column, $attributes);

        if (!is_array($value) && !$value instanceof \Countable) {
            throw new \Exception('The value in a CountCell must be an array or an instance of \Countable');
        }

        return count($value);
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
