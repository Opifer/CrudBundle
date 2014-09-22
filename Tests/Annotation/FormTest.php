<?php

namespace Opifer\CrudBundle\Tests\Annotation;

use Opifer\CrudBundle\Annotation\Form as FormAnnotation;

class FormTest extends \PHPUnit_Framework_TestCase
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
