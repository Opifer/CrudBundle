<?php

namespace Opifer\CrudBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Opifer\CrudBundle\Datagrid\GridType\SimpleGridType;

class CrudController extends Controller
{
    /**
     * Render the entity index
     *
     * @param Request $request
     * @param object  $entity
     * @param string  $slug
     *
     * @return Response
     */
    public function indexAction(Request $request, $entity, $slug)
    {
        $datagrid = $this->get('opifer.crud.datagrid_factory')->create(new SimpleGridType(), $entity);

        return $this->render('OpiferCrudBundle:Crud:list.html.twig', [
            'slug'  => $slug,
            'grid'  => $datagrid,
            'query' => array_merge($request->query->all(), ['slug' => $slug]),
        ]);
    }

    /**
     * Create new item
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
                    $getRelations = 'get' . ucfirst($relation['fieldName']);
                    foreach ($form->getData()->$getRelations() as $relationClass) {
                        $setRelation = 'set' . ucfirst($relation['mappedBy']);
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
            'entity' => $entity
        ]);
    }

    /**
     * Edit an item
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
        $relations = $this->get('opifer.crud.entity_helper')->getRelations($entity);

        // Set original relations, to be used after form's isValid method passed
        foreach ($relations as $key => $relation) {
            if ($relation['isOwningSide'] === false) {
                $originalRelations[$key] = new ArrayCollection();
                $getRelations = 'get' . ucfirst($relation['fieldName']);

                foreach ($entity->$getRelations() as $relationEntity) {
                    $originalRelations[$key]->add($relationEntity);
                }
            }
        }

        $form = $this->createForm($this->get('opifer.crud.crud_type'), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($relations as $key => $relation) {
                if ($relation['isOwningSide'] === false) {
                    // Set getters and setters
                    $getRelations = 'get' . ucfirst($relation['fieldName']);
                    $setRelation = 'set' . ucfirst($relation['mappedBy']);

                    // Connect the added relations
                    foreach ($form->getData()->$getRelations() as $relationClass) {
                        $relationClass->$setRelation($entity);
                    }

                    // Disconnect the removed relations
                    foreach ($originalRelations[$key] as $relationEntity) {
                        if (false === $entity->$getRelations()->contains($relationEntity)) {
                            $relationEntity->$setRelation(null);

                            $em->persist($relationEntity);
                        }
                    }
                }
            }
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('crud.edit.success'));

            return $this->redirect($this->generateUrl('opifer.crud.index', [
                'slug' => $slug
            ]));
        }

        return $this->render('OpiferCrudBundle:Crud:edit.html.twig', [
            'form'   => $form->createView(),
            'slug'   => $slug,
            'entity' => $entity
        ]);
    }

    /**
     * Remove an item
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
            'slug' => $slug
        ]));
    }
}
