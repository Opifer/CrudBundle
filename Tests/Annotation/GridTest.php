<?php

namespace Opifer\CrudBundle\Tests\Annotation;

use Opifer\CrudBundle\Annotation\Grid as GridAnnotation;

class GridTest extends \PHPUnit_Framework_TestCase
{
    protected $annotationClass;

    public function __construct()
    {
        $this->annotationClass = new GridAnnotation(array());
    }

    public function testListableDefault()
    {
        $expected = false;
        $actual = $this->annotationClass->listable;

        $this->assertEquals($expected, $actual);
    }
}
