<?php

namespace Opifer\CrudBundle\Tests\Annotation;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Opifer\CrudBundle\Annotation\Form as FormAnnotation;

class FormTest extends WebTestCase
{
    protected $annotationClass;

    public function __construct()
    {
        $this->annotationClass = new FormAnnotation(array());
    }

    public function testEditableDefault()
    {
        $expected = false;
        $actual = $this->annotationClass->editable;

        $this->assertEquals($expected, $actual);
    }
}
