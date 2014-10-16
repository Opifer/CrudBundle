<?php

namespace Opifer\CrudBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class ArrayToJsonTransformer implements DataTransformerInterface
{
    /**
     * Transforms an array to a string.
     *
     * @return string
     */
    public function transform($array)
    {
        return json_encode($array);
    }

    /**
     * Transforms a string to an array.
     *
     * @param string $string
     *
     * @return array
     */
    public function reverseTransform($string)
    {
        return json_decode($string, true);
    }
}
