<?php

namespace Opifer\CrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Opifer\CrudBundle\Handler\RequestHandler;

class ApiController extends Controller
{
    /**
     * Index
     *
     * @param Request $request
     * @param object  $entity
     *
     * @return Response
     */
    public function indexAction(Request $request, $entity)
    {
        // @todo, retrieve by query parameters
        $collection = $this->getDoctrine()->getRepository(get_class($entity))
            ->findAll();

        $data = $this->get('jms_serializer')->serialize($collection, 'json');

        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * View
     *
     * @param Request $request
     * @param object  $entity
     *
     * @return Response
     */
    public function viewAction(Request $request, $entity)
    {
        $data = $this->get('jms_serializer')->serialize($entity, 'json');

        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Create
     *
     * @param Request $request
     * @param object  $entity
     *
     * @return Response
     */
    public function createAction(Request $request, $entity)
    {
        return $this->forward('OpiferCrudBundle:Api:store', ['request' => $request, 'entity' => $entity]);
    }

    /**
     * Update
     *
     * @param Request $request
     * @param object  $entity
     *
     * @return Response
     */
    public function updateAction(Request $request, $entity)
    {
        return $this->forward('OpiferCrudBundle:Api:store', ['request' => $request, 'entity' => $entity]);
    }

    /**
     * Store
     *
     * @param Request $request
     * @param object  $entity
     *
     * @return Response
     */
    public function storeAction(Request $request, $entity)
    {
        $handler = new RequestHandler();
        $handler->handleRequest($request, $entity);

        $validator = $this->get('validator');
        $errors = $validator->validate($entity);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response('{"success":false, "errors":"'.$errorsString.'"}', 200, ['Content-Type' => 'application/json']);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new Response('{"success":true}', 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Delete
     *
     * @param Request $request
     * @param object  $entity
     *
     * @return Response
     */
    public function deleteAction(Request $request, $entity)
    {
        $em = $this->get('doctrine')->getManager();

        try {
            $em->remove($entity);
            $em->flush();
        } catch (\Exception $e) {
            return new Response('{"success":false, "errors":""}', 200, ['Content-Type' => 'application/json']);
        }

        return new Response('{"success":true}', 200, ['Content-Type' => 'application/json']);
    }
}
