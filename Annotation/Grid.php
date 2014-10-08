<?php

namespace Opifer\CrudBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * The Opifer Crud annotation.
 * Usage:
 * - add a use statement of this class to the entity (e.g. use Opifer\CrudBundle\Annotation\Grid;)
 * - add the annotation to the properties. (e.g. @Grid(editable=true, linkable=true) )
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Grid extends Annotation
{
    /**
     * Determines whether the property should be shown in the CRUD list
     *
     * @var boolean
     */
    public $listable = false;

    /**
     * @var string
     */
    public $type;
}
