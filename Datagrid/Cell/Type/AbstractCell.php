<?php

namespace Opifer\CrudBundle\Datagrid\Cell\Type;

use Opifer\CrudBundle\Datagrid\Column\Column;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractCell implements CellTypeInterface
{
    /**
     * {@inheritDoc}
     */
    public function getData($row, Column $column, array $attributes)
    {
        $accessor = PropertyAccess::getPropertyAccessor();

        return $accessor->getValue($row, $column->getProperty());
    }

    /**
     * {@inheritDoc}
     */
    public function getExportData($row, Column $column, array $attributes)
    {
        return $this->getData($row, $column, $attributes);
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
        return 'text';
    }
}
