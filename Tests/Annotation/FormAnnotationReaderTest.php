<?php

namespace Opifer\CrudBundle\Tests\Annotation;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Opifer\CrudBundle\Annotation\FormAnnotationReader;
use Opifer\CrudBundle\Annotation as Opifer;

class FormAnnotationReaderTest extends WebTestCase
{
    private $annotationReader;

    public function __construct()
    {
        $this->annotationReader = new FormAnnotationReader();
    }

    public function testGet()
    {
        $expected = array('description');
        $actual = $this->annotationReader->get(new FakeFormEntity(), 'editable');

        $this->assertEquals($expected, $actual);
    }

    public function testGetEditableProperties()
    {
        $expected = array('description');
        $actual = $this->annotationReader->getEditableProperties(new FakeFormEntity());

        $this->assertEquals($expected, $actual);
    }

    public function testIs()
    {
        $expected = true;
        $actual = $this->annotationReader->is(new FakeFormEntity(), 'description', 'editable');

        $this->assertEquals($expected, $actual);
    }

    public function testIsEditable()
    {
        $expected = true;
        $actual = $this->annotationReader->isEditable(new FakeFormEntity(), 'description');

        $this->assertEquals($expected, $actual);
    }
}

class FakeFormEntity
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var text
     * @Opifer\Form(editable=true)
     */
    protected $description;
}
