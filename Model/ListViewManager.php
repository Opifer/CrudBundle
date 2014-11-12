<?php

namespace Opifer\CrudBundle\Model;

use Doctrine\ORM\EntityManager;
use JMS\Serializer\Serializer;
use Opifer\CrudBundle\Datagrid\Datagrid;
use Opifer\CrudBundle\Form\Type\ListViewType;
use Opifer\CrudBundle\Doctrine\EntityHelper;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ListViewManager
{
    /** @var EntityManager */
    protected $em;

    /** @var Serializer */
    protected $serializer;

    /** @var EntityHelper */
    protected $entityHelper;

    /** @var FormFactoryInterface */
    protected $formFactory;

    /**
     * Constructor
     *
     * @param EntityManager $em
     * @param Serializer    $serializer
     */
    public function __construct(EntityManager $em, Serializer $serializer, EntityHelper $entityHelper, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->entityHelper = $entityHelper;
        $this->formFactory = $formFactory;
    }

    /**
     * Handle a ListView form
     *
     * @param  Request  $request
     * @param  Datagrid $datagrid
     *
     * @return \Symfony\Component\Form\FormView
     */
    public function handleForm(Request $request, Datagrid $datagrid)
    {
        // Clone the view, so the current view won't get changed by the empty view form
        $view = clone $datagrid->getView();

        $columns = $this->entityHelper->getAllProperties(
            $view->getEntity()
        );
        
        $type = new ListViewType($datagrid->getSource(), $columns);
        $viewForm = $this->formFactory->create($type, $view);
        $viewForm->handleRequest($request);

        if ($viewForm->get('save')->isClicked()) {
            if ($viewForm->isValid()) {
                $this->save($view);
                
                $request->request->set('view', $view->getSlug());
            } else {
                throw new \Exception('The view could not be saved because the form was invalid.');
            }
        }

        return $viewForm->createView();
    }

    /**
     * Save a listview
     *
     * @param  ListView $view
     *
     * @return ListView
     */
    public function save(ListView $view)
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

    /**
     * Get the repository
     *
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('Opifer\CrudBundle\Entity\ListView');
    }
}
