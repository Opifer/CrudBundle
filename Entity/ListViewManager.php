<?php

namespace Opifer\CrudBundle\Entity;

use Doctrine\ORM\EntityManager;
use JMS\Serializer\Serializer;

class ListViewManager
{
    /** @param EntityManager $em */
    protected $em;

    protected $serializer;

    /**
     * Constructor
     *
     * @param EntityManager $em
     * @param Serializer    $serializer
     */
    public function __construct(EntityManager $em, Serializer $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }

    /**
     * Handle the view form
     *
     * @param object $formdata
     *
     * @return ListView
     */
    public function handleForm(ListView $view)
    {
        $data = $this->serializer->deserialize($view->getConditions(), 'Opifer\RulesEngine\Rule\Rule', 'json');
        $view->setConditions($data);

        // Transform column data to the correct format.
        // $columns = [];
        // foreach ($view->getColumns() as $column) {
        //     $columns[] = [
        //         'property' => $column,
        //         'type' => 'string' // Change to get the right type
        //     ];
        // }
        // $view->setColumns(json_encode($columns));

        $this->em->persist($view);
        $this->em->flush();

        return $view;
    }
}
