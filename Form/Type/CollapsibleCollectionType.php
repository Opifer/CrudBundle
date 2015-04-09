<?php

namespace Opifer\CrudBundle\Form\Type;


use Braincrafted\Bundle\BootstrapBundle\Form\Type\BootstrapCollectionType;

class CollapsibleCollectionType extends BootstrapCollectionType
{

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'collapsible_collection';
    }


}