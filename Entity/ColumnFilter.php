<?php

namespace Opifer\CrudBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * ColumnFilter
 *
 * @ORM\Entity
 */
class ColumnFilter extends CrudFilter
{
    /**
     * @var string
     *
     * @ORM\Column(name="filter", type="text")
     * @Assert\NotBlank()
     */
    protected $columns;

    public function getColumns()
    {
        return $this->columns;
    }

    public function setColumns($columns)
    {
        $this->columns = $columns;

        return $this;
    }
}
