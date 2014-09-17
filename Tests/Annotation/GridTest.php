<?php

namespace Opifer\CrudBundle\Tests\Annotation;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Opifer\CrudBundle\Annotation\Grid as GridAnnotation;

class GridTest extends WebTestCase
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
