<?php

namespace Opifer\CrudBundle\Datagrid\Cell\Type;

class CheckboxCell extends AbstractCell
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
