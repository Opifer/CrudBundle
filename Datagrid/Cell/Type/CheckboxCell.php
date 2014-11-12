<?php

namespace Opifer\CrudBundle\Datagrid\Cell\Type;

class CheckboxCell extends PropertyCell
{
    /**
     * {@inheritDoc}
     */
    public function getView()
    {
        return 'checkbox';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'checkbox';
    }
}
