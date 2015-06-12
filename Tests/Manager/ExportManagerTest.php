<?php

namespace Opifer\CrudBundle\Tests\Datagrid;

use Mockery as m;
use Opifer\CrudBundle\Datagrid\Grid\CrudGrid;
use Opifer\CrudBundle\Manager\ExportManager;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Opifer\CrudBundle\Datagrid\Datagrid;
use Opifer\CrudBundle\Tests\TestData\User;

class ExportManagerTest extends \PHPUnit_Framework_TestCase
{
    private $builder;
    private $exportStrategy;

    public function setUp()
    {
        $this->builder = m::mock('Opifer\CrudBundle\Datagrid\DatagridBuilder');
        $this->exportStrategy = m::mock('Opifer\CrudBundle\Export\ExportInterface');
    }

    public function testExportGrid()
    {
        $manager = new ExportManager($this->builder);
        $manager->setExportStrategy($this->exportStrategy);

        $this->assertEquals($this->exportStrategy, $manager->getExportStrategy());
    }

    public function testExportGridWithPaginator()
    {
        $datagrid = new Datagrid();
        $simpleGrid = new CrudGrid('users');
        $user = new User();

        $this->builder->shouldReceive('create')->andReturn($this->builder);
        $this->builder->shouldReceive('setLimit')->with(1000);
        $this->builder->shouldReceive('setPage')->with(1);
        $this->builder->shouldReceive('build')->andReturn($datagrid);

        $this->exportStrategy->shouldReceive('createExportObject')->with($datagrid);
        $this->exportStrategy->shouldReceive('writeData')->with($datagrid->getRows());
        $this->exportStrategy->shouldReceive('createResponse')->andReturn(new StreamedResponse());

        $manager = new ExportManager($this->builder);
        $manager->setExportStrategy($this->exportStrategy);
        $response = $manager->exportGrid($simpleGrid, $user);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
    }

    public function testExportGridWithoutPaginator()
    {
        $datagrid = \Mockery::mock('Opifer\CrudBundle\Datagrid\Datagrid')->makePartial();
        $datagrid->shouldReceive('getPaginator')->andReturn(null);
        $simpleGrid = new CrudGrid('users');
        $user = new User();

        $this->builder->shouldReceive('create')->andReturn($this->builder);
        $this->builder->shouldReceive('setLimit')->with(1000);
        $this->builder->shouldReceive('setPage')->with(1);
        $this->builder->shouldReceive('build')->andReturn($datagrid);

        $this->exportStrategy->shouldReceive('createExportObject')->with($datagrid);
        $this->exportStrategy->shouldReceive('writeData')->with($datagrid->getRows());
        $this->exportStrategy->shouldReceive('createResponse')->andReturn(new StreamedResponse());

        $manager = new ExportManager($this->builder);
        $manager->setExportStrategy($this->exportStrategy);
        $response = $manager->exportGrid($simpleGrid, $user);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
    }
}
