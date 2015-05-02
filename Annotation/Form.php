<?php

namespace Opifer\CrudBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * The Opifer Form annotation.
 * Usage:
 * - add a use statement of this class to the entity (e.g. use Opifer\CrudBundle\Annotation\Form;)
 * - add the annotation to the properties. (e.g. @Form(editable=true) )
 *
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
final class Form extends Annotation
{
    /**
     * Determines whether the property should be editable in the create/edit forms
     *
     * @var boolean
     */
    public $editable = false;

    /**
     * Specifies the Form Type that should be used in Forms
     *
     * @var string
     */
    public $type;

    /**
     * @var array<string>
     */
    public $options = array();
}
