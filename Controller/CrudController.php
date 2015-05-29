<?php

namespace Opifer\CrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Opifer\CrudBundle\Datagrid\Grid\CrudGrid;

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
        $datagrid = $this->get('opifer.crud.datagrid_factory')->create(new CrudGrid($slug), $entity);

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
        $formType = $this->get('opifer.crud.form_annotation_reader')->getClassType($entity);
        $form = $this->createForm(($formType) ? $formType : $this->get('opifer.crud.crud_type'), $entity);
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
        $formType = $this->get('opifer.crud.form_annotation_reader')->getClassType($entity);
        $relations = $this->get('opifer.crud.entity_helper')->getRelations($entity);

        // Set original relations, to be used after form's isValid method passed
        $originalRelations = $this->get('opifer.crud.relation_manager')->originalRelations([], $relations, $entity);

        $form = $this->createForm(($formType) ? $formType : $this->get('opifer.crud.crud_type'), $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $this->get('opifer.crud.relation_manager')->setRelations($relations, $originalRelations, $entity);
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

    /**
     * Batch delete action
     *
     * @param Request $request
     * @param string  $slug
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
            'slug' => $slug
        ]));
    }

    /**
     * @param Object $entity
     * @param string $slug
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     *
     * @throws \PHPExcel_Exception
     */
    public function exportAction($entity, $slug)
    {
        $serializer = $this->get('serializer');
        $datagrid = $this->get('opifer.crud.datagrid_factory')->create(new CrudGrid($slug), $entity);

        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // Set the column headers
        $c = 'A';
        foreach ($columns = $datagrid->getColumns() as $column) {
            $phpExcelObject->getActiveSheet()->setCellValue($c.'1', $column->getLabel());

            $c++;
        }

        // Add the rows
        $r = 2;
        while ($datagrid->getPaginator()->hasNextPage()) {
            $datagrid->getPaginator()->getNextPage();

            foreach ($datagrid->getRows() as $row) {
                $c = 'A';
                foreach ($row->getCells() as $cel) {
                    $data = $cel->getValue();

                    if (is_array($data)) {
                        $data = implode(',', $data);
                    } else if (is_object($data)) {
                        $data = $serializer->serialize($data, 'json');
                    }

                    $phpExcelObject->getActiveSheet()->setCellValue($c.$r, $data);

                    $c++;
                }

                $r++;
            }
        }

        $phpExcelObject->getActiveSheet()->setTitle('Datagrid export');

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=datagrid-export.xls');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }
}
