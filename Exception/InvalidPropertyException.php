<?php

namespace Opifer\CrudBundle\Exception;

class InvalidPropertyException extends \Exception
{
    public function __construct($object, $method)
    {
        $property = str_replace('set', '', $method);
        $property = lcfirst($property);

        parent::__construct('Object "'.get_class($object).'" does not have a method called "'.$method.'", generated from property "'.$property.'"');
    }
}
