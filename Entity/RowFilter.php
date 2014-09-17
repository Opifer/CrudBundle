<?php

namespace Opifer\CrudBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

use Symfony\Component\Validator\Constraints as Assert;

use Opifer\CrudBundle\Annotation as CRUD;

/**
 * Filter
 *
 * @ORM\Entity
 */
class RowFilter extends CrudFilter
{
    /**
     * @var string
     *
     * @CRUD\Form(editable=true, type="ruleeditor", options={"provider" : "crud"})
     * @ORM\Column(name="conditions", type="object")
     * @Assert\NotBlank()
     *
     * @JMS\Type("Opifer\RulesEngine\Rule\Rule")
     */
    protected $conditions;

    /**
     * Set condition
     *
     * @param  string     $conditions
     * @return CrudFilter
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;

        return $this;
    }

    /**
     * Get conditions
     *
     * @return Conditions
     */
    public function getConditions()
    {
        return $this->conditions;
    }
}
