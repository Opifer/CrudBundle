<?php

namespace Opifer\CrudBundle\Exception;

class IncorrectRouteConfigException extends \Exception
{
    protected $message = 'The route you were trying to access is not configured correctly in the CrudBundle config.';
}
