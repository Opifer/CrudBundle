<?php

namespace Opifer\CrudBundle\Tests\Form\DataTransformer;

use Opifer\CrudBundle\Form\DataTransformer\ArrayToJsonTransformer;

class ArrayToJsonTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $transformer = new ArrayToJsonTransformer();

        $array = ['one' => 'two', 'three' => 'four'];

        $expected = '{"one":"two","three":"four"}';
        $actual = $transformer->transform($array);
        
        $this->assertEquals($expected, $actual);
    }

    public function reverseTransform()
    {
        $transformer = new ArrayToJsonTransformer();

        $expected = ['one' => 'two', 'three' => 'four'];

        $string = '{"one":"two","three":"four"}';
        $actual = $transformer->reverseTransform($string);
        
        $this->assertEquals($expected, $actual);
    }
}
