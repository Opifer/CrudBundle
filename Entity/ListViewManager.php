<?php

namespace Opifer\CrudBundle\Entity;

use Doctrine\ORM\EntityManager;

class ListViewManager
{
    /** @param EntityManager $em */
    protected $em;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Handle the view form
     *
     * @param object $entity
     * @param object $formdata
     *
     * @return GridFilter
     */
    public function handleForm($entity, $formdata)
    {
        $view = new ListView();
        $view->setName($formdata->getName());
        $view->setEntity(get_class($entity));

        // Transform column data to the correct format.
        $columns = [];
        foreach ($formdata->getColumns() as $column) {
            $columns[] = [
                'property' => $column,
                'type' => 'string' // Change to get the right type
            ];
        }
        $view->setColumns(json_encode($columns));

        $this->em->persist($view);
        $this->em->flush();

        return $view;
    }
}
