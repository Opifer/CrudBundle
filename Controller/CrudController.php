<?php

namespace Opifer\CrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Opifer\CrudBundle\Datagrid\Grid\CrudGrid;

class CrudController extends Controller
{
    /**
     * Render the entity index.
     *
     * @param Request $request
     * @param object  $entity
     * @param string  $slug
     *
     * @return Response
     */
    public function indexAction(Request $request, $entity, $slug)
    {
        $datagrid = $this->get('opifer.crud.datagrid_factory')->create(new CrudGrid($slug), $entity);

        return $this->render('OpiferCrudBundle:Crud:list.html.twig', [
            'slug'  => $slug,
            'grid'  => $datagrid,
            'query' => array_merge($request->query->all(), ['slug' => $slug]),
        ]);
    }

    /**
     * Create new item.
     *
     * @param Request $request
     * @param object  $entity
     * @param string  $slug
     *
     * @return Response
     */
    public function newAction(Request $request, $entity, $slug)
    {
        $form = $this->createForm($this->get('opifer.crud.crud_type'), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($this->get('opifer.crud.entity_helper')->getRelations($entity) as $key => $relation) {
                if ($relation['isOwningSide'] === false) {
                    $getRelations = 'get'.ucfirst($relation['fieldName']);
                    foreach ($form->getData()->$getRelations() as $relationClass) {
                        $setRelation = 'set'.ucfirst($relation['mappedBy']);
                        $relationClass->$setRelation($entity);
                    }
                }
            }

            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('crud.new.success'));

            return $this->redirect($this->generateUrl('opifer.crud.index', ['slug' => $slug]));
        }

        return $this->render('OpiferCrudBundle:Crud:edit.html.twig', [
            'form'   => $form->createView(),
            'slug'   => $slug,
            'entity' => $entity,
        ]);
    }

    /**
     * Edit an item.
     *
     * @param Request $request
     * @param object  $entity
     * @param string  $slug
     * @param integer $id
     *
     * @return Response
     */
    public function editAction(Request $request, $entity, $slug, $id)
    {
        $entity = $this->getDoctrine()->getRepository(get_class($entity))->find($id);

        $form = $this->createForm($this->get('opifer.crud.crud_type'), $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            // Connect the added relations
            $this->get('opifer.crud.entity_helper')->connectAddedRelations($data);

            // Disconnect the removed relations
            $this->get('opifer.crud.entity_helper')->disconnectRemovedRelations($data, $entity);

            $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('crud.edit.success'));

            return $this->redirect($this->generateUrl('opifer.crud.index', [
                'slug' => $slug,
            ]));
        }

        return $this->render('OpiferCrudBundle:Crud:edit.html.twig', [
            'form'   => $form->createView(),
            'slug'   => $slug,
            'entity' => $entity,
        ]);
    }

    /**
     * Remove an item.
     *
     * When the Softdeletable annotation (@Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false))
     * is set on the entity, the item will be removed from the index, but will still
     * exist in the database with a timestamp on the deleted_at column.
     *
     * @param object  $entity
     * @param string  $slug
     * @param integer $id
     *
     * @return Response
     */
    public function deleteAction($entity, $slug, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(get_class($entity))->find($id);

        $em->remove($entity);
        $em->flush();

        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('crud.delete.success'));

        return $this->redirect($this->generateUrl('opifer.crud.index', [
            'slug' => $slug,
        ]));
    }

    /**
     * Batch delete action.
     *
     * @param Request $request
     * @param string  $action
     *
     * @return Response
     */
    public function batchDeleteAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->get('opifer.crud.slug_transformer')
            ->transform($slug);

        $objects = $em->getRepository(get_class($entity))->createQueryBuilder('a')
            ->where('a.id IN (:ids)')
            ->setParameter('ids', $request->get('batchselect'))
            ->getQuery()
            ->getResult();

        foreach ($objects as $object) {
            $em->remove($object);
        }

        $em->flush();

        return $this->redirect($this->generateUrl('opifer.crud.index', [
            'slug' => $slug,
        ]));
    }
}
