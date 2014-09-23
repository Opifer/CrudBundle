<?php

namespace Opifer\CrudBundle\Tests\Datagrid;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\CrudBundle\Tests\TestData\User;
use Opifer\CrudBundle\Datagrid\DatagridBuilder;

class DatagridBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $container;

    public function __construct()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\Container');
    }

    public function testCreate()
    {
        $filterRepository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $filterRepository->expects($this->any())
            ->method('columnFilters')
            ->will($this->returnValue(new ArrayCollection()));

        $filterRepository->expects($this->any())
            ->method('rowFilters')
            ->will($this->returnValue(new ArrayCollection()));

        $datagridBuilder = $this->getMockBuilder('Opifer\CrudBundle\Datagrid\DatagridBuilder')
            ->setMethods(array('getFilterRepository'))
            ->setConstructorArgs(array($this->container))
            ->getMock();

        $datagridBuilder->expects($this->any())
            ->method('getFilterRepository')
            ->will($this->returnValue($filterRepository));

        $actual = $datagridBuilder->create(new User());

        $this->assertInstanceOf('Opifer\CrudBundle\Datagrid\DatagridBuilder', $actual);
    }

    public function testAddColumn()
    {
        $filterRepository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $filterRepository->expects($this->any())
            ->method('columnFilters')
            ->will($this->returnValue(new ArrayCollection()));

        $filterRepository->expects($this->any())
            ->method('rowFilters')
            ->will($this->returnValue(new ArrayCollection()));

        $datagridBuilder = $this->getMockBuilder('Opifer\CrudBundle\Datagrid\DatagridBuilder')
            ->setMethods(array('getFilterRepository'))
            ->setConstructorArgs(array($this->container))
            ->getMock();

        $datagridBuilder->expects($this->any())
            ->method('getFilterRepository')
            ->will($this->returnValue($filterRepository));

        $datagrid = $datagridBuilder->create(new User())
            ->addColumn('name', 'text', ['label' => 'Username'])
        ;

        $this->assertCount(1, $datagrid->getColumns());
        $first = $datagrid->getColumns()->first();

        $this->assertInstanceOf('Opifer\CrudBundle\Datagrid\Column\Column', $first);
        $this->assertEquals('name', $first->getProperty());
        $this->assertEquals('text', $first->getType());
        $this->assertEquals('Username', $first->getLabel());
    }
}
