<?php

namespace Opifer\CrudBundle\Tests\Annotation;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Opifer\CrudBundle\Annotation\GridAnnotationReader;
use Opifer\CrudBundle\Annotation as Opifer;

class GridAnnotationReaderTest extends WebTestCase
{
    private $annotationReader;

    public function __construct()
    {
        $this->annotationReader = new GridAnnotationReader();
    }

    public function testGet()
    {
        $expected = array('id', 'title');
        $actual = $this->annotationReader->get(new FakeGridEntity(), 'listable');

        $this->assertEquals($expected, $actual);
    }

    public function testGetListableProperties()
    {
        $expected = array('id', 'title');
        $actual = $this->annotationReader->getListableProperties(new FakeGridEntity());

        $this->assertEquals($expected, $actual);
    }

    public function testIs()
    {
        $expected = true;
        $actual = $this->annotationReader->is(new FakeGridEntity(), 'title', 'listable');

        $this->assertEquals($expected, $actual);
    }
}

class FakeGridEntity
{
    /**
     * @var integer
     *
     * @Opifer\Grid(listable=true)
     */
    protected $id;

    /**
     * @var string
     *
     * @Opifer\Grid(listable=true)
     */
    protected $title;

    /**
     * @var text
     */
    protected $description;
}
