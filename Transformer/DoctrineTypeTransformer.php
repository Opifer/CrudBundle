<?php

namespace Opifer\CrudBundle\Transformer;

class DoctrineTypeTransformer
{
    /**
     * Transforms Doctrine types to Symfony's form types
     *
     * @param string $type
     *
     * @return string
     */
    public function transform($type)
    {
        switch ($type) {
            case 'text':
                return 'textarea';
            case 'boolean':
                return 'checkbox';
            case 'smallint':
            case 'integer':
            case 'bigint':
                return 'integer';
            case 'decimal':
            case 'float':
                return 'number';
            case 'date':
                return 'date';
            case 'datetime':
                return 'datetime';
            case 'time':
                return 'time';
            case 'array':
            case 'simple_array':
            case 'json_array':
                return 'bootstrap_collection';
            default:
                return 'text';
        }
    }
}
