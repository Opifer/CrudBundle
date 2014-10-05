<?php

namespace Opifer\CrudBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Opifer\CrudBundle\Entity\CrudFilter;
use Opifer\CrudBundle\Entity\ColumnFilter;
use Opifer\CrudBundle\Entity\RowFilter;
use Opifer\CrudBundle\Form\Type\ColumnFilterType;
use Opifer\CrudBundle\Form\Type\RowFilterType;

class FilterController extends Controller
{
    /**
     * @param Request $request
     * @param string  $slug
     *
     * @Route(
     *     "/{slug}/filter/column/new",
     *     name="opifer.crud.filter.column.new"
     * )
     *
     * @return Response
     */
    public function newColumnFilterAction(Request $request, $slug)
    {
        $entity = $this->get('opifer.crud.slug_to_entity_transformer')->transform($slug);

        $properties = $this->get('opifer.crud.entity_helper')->getAllProperties($entity);
        $form = $this->createForm(new ColumnFilterType($properties), new ColumnFilter());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $filter = $this->get('opifer.crud.filter_manager')->handleForm($entity, $form->getData());

            return $this->redirect($this->generateUrl('opifer.crud.filter', [
                'slug' => $slug,
                'columnfilter' => $filter->getSlug()
            ]));
        }

        return $this->render('OpiferCrudBundle:Filter:new.html.twig', [
            'extend_template' => $this->container->getParameter('opifer_crud.extend_template'),
            'slug' => $slug,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param string  $slug
     *
     * @Route(
     *     "/{slug}/filter/row/new",
     *     name="opifer.crud.filter.row.new"
     * )
     *
     * @return Response
     */
    public function newRowFilterAction(Request $request, $slug)
    {
        $entity = $this->get('opifer.crud.slug_to_entity_transformer')->transform($slug);

        $rowFilter = new RowFilter();
        $rowFilter->setEntity(get_class($entity));

        $form = $this->createForm(new RowFilterType($entity), $rowFilter);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $filter = $form->getData();

            $data = $this->get('jms_serializer')->deserialize($filter->getConditions(), 'Opifer\RulesEngine\Rule\Rule', 'json');
            $filter->setConditions($data);

            $em = $this->getDoctrine()->getManager();
            $em->persist($filter);
            $em->flush();

            return $this->redirect($this->generateUrl('opifer.crud.filter', [
                'rowfilter' => $filter->getSlug(),
                'slug' => $slug
            ]));
        }

        return $this->render('OpiferCrudBundle:Filter:new.html.twig', [
            'extend_template' => $this->container->getParameter('opifer_crud.extend_template'),
            'slug' => $slug,
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete the filter
     *
     * @param string  $slug
     * @param integer $id
     *
     * @Route(
     *     "/{slug}/filter/delete/{id}",
     *     name="opifer.crud.filter.delete",
     *     requirements={"id" = "\d+"}
     * )
     *
     * @return Response
     */
    public function deleteAction($slug, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('OpiferCrudBundle:CrudFilter')->find($id);

        $em->remove($filter);
        $em->flush();

        return $this->redirect($this->generateUrl('opifer.crud.index', [
            'slug' => $slug
        ]));
    }
}
