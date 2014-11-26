<?php

namespace Opifer\CrudBundle\Tests\Form\Type;

use Mockery as m;
use Opifer\CrudBundle\Form\Type\CrudRelationType;
use Opifer\CrudBundle\Tests\TestData\User;

class CrudRelationTypeTest extends \PHPUnit_Framework_TestCase
{
    private $entityHelper;
    private $object;
    private $annotationReader;

    public function setUp()
    {
        $this->entityHelper = m::mock('Opifer\CrudBundle\Doctrine\EntityHelper');
        $this->object = new User();
        $this->annotationReader = m::mock('Opifer\CrudBundle\Annotation\FormAnnotationReader');
    }

    public function testPropertyIsAllowed()
    {
        $type = m::mock(
            'Opifer\CrudBundle\Form\Type\CrudRelationType[getAllowedProperties]',
            [$this->entityHelper, $this->object, $this->annotationReader]
        );
        $type->shouldReceive('getAllowedProperties')->andReturn(['id', 'name', 'email']);

        $this->assertTrue($type->isAllowed('name'));
    }

    public function testPropertyIsNotAllowed()
    {
        $type = m::mock(
            'Opifer\CrudBundle\Form\Type\CrudRelationType[getAllowedProperties]',
            [$this->entityHelper, $this->object, $this->annotationReader]
        );
        $type->shouldReceive('getAllowedProperties')->andReturn(['id', 'name', 'email']);

        $this->assertFalse($type->isAllowed('firstName'));
    }

    public function testGetName()
    {
        $type = new CrudRelationType($this->entityHelper, $this->object, $this->annotationReader);

        $this->assertEquals('crud_relation', $type->getName());
    }
}
