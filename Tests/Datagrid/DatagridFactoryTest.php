<?php

namespace Opifer\CrudBundle\Tests\Datagrid;

use Mockery as m;
use Opifer\CrudBundle\Datagrid\Datagrid;
use Opifer\CrudBundle\Datagrid\DatagridFactory;
use Opifer\CrudBundle\Datagrid\Grid\SimpleGrid;
use Opifer\CrudBundle\Tests\TestData\User;

class DatagridFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $builder;

    public function setUp()
    {
        $this->builder = m::mock('Opifer\CrudBundle\Datagrid\DatagridBuilder');
    }

    public function testCreate()
    {
        $datagrid = new Datagrid();
        $simpleGrid = new SimpleGrid();
        $user = new User();

        $this->builder->shouldReceive('create')->andReturn($this->builder);
        $this->builder->shouldReceive('build')->andReturn($datagrid);

        $factory = new DatagridFactory($this->builder);
        $actual = $factory->create($simpleGrid, $user);

        $this->assertInstanceOf('Opifer\CrudBundle\Datagrid\Datagrid', $actual);
    }

    public function tearDown()
    {
        m::close();
    }
}
