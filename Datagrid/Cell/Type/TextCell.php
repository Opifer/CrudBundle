<?php

namespace Opifer\CrudBundle\Datagrid\Cell\Type;

use Opifer\CrudBundle\Datagrid\Column\Column;

class TextCell extends AbstractCell
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'text';
    }
}
