<?php
/**
 * Created by PhpStorm.
 * User: sander
 * Date: 08/04/15
 * Time: 16:18
 */

namespace Opifer\CrudBundle\Form\Type;


use Braincrafted\Bundle\BootstrapBundle\Form\Type\BootstrapCollectionType;

class CollapsibleCollectionType extends BootstrapCollectionType
{
    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'bootstrap_collection';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'collapsible_collection';
    }


}