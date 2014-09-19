<?php

namespace Opifer\CrudBundle\Handler;

use Symfony\Component\HttpFoundation\Request;

use Opifer\CrudBundle\Exception\InvalidJsonRequestException;
use Opifer\CrudBundle\Exception\InvalidPropertyException;

class RequestHandler
{
    public function handleRequest(Request $request, $entity)
    {
        $data = $this->convertParams($request);

        foreach ($data as $key => $value) {
            $setProperty = 'set'.ucfirst($key);

            if (!method_exists($entity, $setProperty)) {
                throw new InvalidPropertyException($entity, $setProperty);
            }

            if (is_array($value)) {
                // @todo Handle relations
            } else {
                $entity->$setProperty($value);
            }
        }

        return $entity;
    }

    public function convertParams($request)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new InvalidJsonRequestException();
        }

        return $data;
    }
}
