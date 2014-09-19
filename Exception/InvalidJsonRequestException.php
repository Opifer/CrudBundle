<?php

namespace Opifer\CrudBundle\Exception;

class InvalidJsonRequestException extends \Exception
{
    protected $message = 'The request is not valid JSON request.';
}
