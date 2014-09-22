<?php

namespace Opifer\CrudBundle\Tests\Annotation;

use Opifer\CrudBundle\Annotation\FormAnnotationReader;
use Opifer\CrudBundle\Tests\TestData\User;

class FormAnnotationReaderTest extends \PHPUnit_Framework_TestCase
{
    private $annotationReader;

    public function __construct()
    {
        $this->annotationReader = new FormAnnotationReader();
    }

    public function testGet()
    {
        $expected = array('email');
        $actual = $this->annotationReader->get(new User(), 'editable');

        $this->assertEquals($expected, $actual);
    }

    public function testGetEditableProperties()
    {
        $expected = array('email');
        $actual = $this->annotationReader->getEditableProperties(new User());

        $this->assertEquals($expected, $actual);
    }

    public function testIs()
    {
        $expected = true;
        $actual = $this->annotationReader->is(new User(), 'email', 'editable');

        $this->assertEquals($expected, $actual);
    }

    public function testIsEditable()
    {
        $expected = true;
        $actual = $this->annotationReader->isEditable(new User(), 'email');

        $this->assertEquals($expected, $actual);
    }
}
