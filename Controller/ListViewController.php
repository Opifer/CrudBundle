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
     * @param  string  $slug
     * @param  integer $id
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
}
