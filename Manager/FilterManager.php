<?php

namespace Opifer\CrudBundle\Manager;

use Doctrine\ORM\EntityManager;

use Opifer\CrudBundle\Entity\ColumnFilter;

class FilterManager
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param array $formdata
     *
     * @return GridFilter
     */
    public function handleForm($entity, $formdata)
    {
        $filter = new ColumnFilter();
        $filter->setName($formdata->getName());
        $filter->setEntity(get_class($entity));

        // Transform column data to the correct format.
        $columns = [];
        foreach ($formdata->getColumns() as $column) {
            $columns[] = [
                'property' => $column,
                'type' => 'string' // Change to get the right type
            ];
        }
        $filter->setColumns(json_encode($columns));

        $this->em->persist($filter);
        $this->em->flush();

        return $filter;
    }
}
