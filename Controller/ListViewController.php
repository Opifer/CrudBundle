<?php

namespace Opifer\CrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Opifer\CrudBundle\Entity\ListView;

class ListViewController extends Controller
{
    /**
     * Delete the filter
     *
     * @param string  $slug
     * @param integer $id
     *
     * @return Response
     */
    public function deleteAction($slug, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('OpiferCrudBundle:ListView')->find($id);

        $em->remove($filter);
        $em->flush();

        return $this->redirect($this->generateUrl('opifer.crud.index', [
            'slug' => $slug
        ]));
    }

    // public function newColumnFilterAction(Request $request, $slug)
    // {
    //     $entity = $this->get('opifer.crud.slug_to_entity_transformer')->transform($slug);

    //     $properties = $this->get('opifer.crud.entity_helper')->getAllProperties($entity);
    //     $form = $this->createForm(new ColumnFilterType($properties), new ColumnFilter());
    //     $form->handleRequest($request);

    //     if ($form->isValid()) {
    //         $filter = $this->get('opifer.crud.filter_manager')->handleForm($entity, $form->getData());

    //         return $this->redirect($this->generateUrl('opifer.crud.filter', [
    //             'slug' => $slug,
    //             'columnfilter' => $filter->getSlug()
    //         ]));
    //     }

    //     return $this->render('OpiferCrudBundle:Filter:new.html.twig', [
    //         'extend_template' => $this->container->getParameter('opifer_crud.extend_template'),
    //         'slug' => $slug,
    //         'form' => $form->createView()
    //     ]);
    // }
}
