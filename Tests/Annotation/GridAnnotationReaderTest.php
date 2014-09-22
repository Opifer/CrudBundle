<?php

namespace Opifer\CrudBundle\Tests\Annotation;

use Opifer\CrudBundle\Annotation\GridAnnotationReader;
use Opifer\CrudBundle\Tests\TestData\User;

class GridAnnotationReaderTest extends \PHPUnit_Framework_TestCase
{
    private $annotationReader;

    public function __construct()
    {
        $this->annotationReader = new GridAnnotationReader();
    }

    public function testGet()
    {
        $expected = array('name', 'email');
        $actual = $this->annotationReader->get(new User(), 'listable');

        $this->assertEquals($expected, $actual);
    }

    public function testGetListableProperties()
    {
        $expected = array('name', 'email');
        $actual = $this->annotationReader->getListableProperties(new User());

        $this->assertEquals($expected, $actual);
    }

    public function testIs()
    {
        $expected = true;
        $actual = $this->annotationReader->is(new User(), 'name', 'listable');

        $this->assertEquals($expected, $actual);
    }
}
