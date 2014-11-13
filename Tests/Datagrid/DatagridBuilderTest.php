<?php

namespace Opifer\CrudBundle\Tests\Datagrid;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\CrudBundle\Tests\TestData\User;
use Opifer\CrudBundle\Datagrid\Cell\Type\TextCell;
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
        $viewRepository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->setMethods(array('findByEntity'))
            ->disableOriginalConstructor()
            ->getMock();
        $viewRepository->expects($this->any())
            ->method('findByEntity')
            ->will($this->returnValue([]));

        $request = new \Symfony\Component\HttpFoundation\Request([], ['listview' => ['conditions' => '{}']]);

        $datagridBuilder = $this->getMockBuilder('Opifer\CrudBundle\Datagrid\DatagridBuilder')
            ->setMethods(array('getViewRepository', 'getRequest'))
            ->setConstructorArgs(array($this->container))
            ->getMock();
        $datagridBuilder->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));
        $datagridBuilder->expects($this->any())
            ->method('getViewRepository')
            ->will($this->returnValue($viewRepository));

        $actual = $datagridBuilder->create(new User());

        $this->assertInstanceOf('Opifer\CrudBundle\Datagrid\DatagridBuilder', $actual);
    }

    public function testAddColumn()
    {
        $viewRepository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->setMethods(array('findByEntity'))
            ->disableOriginalConstructor()
            ->getMock();
        $viewRepository->expects($this->any())
            ->method('findByEntity')
            ->will($this->returnValue([]));

        $request = new \Symfony\Component\HttpFoundation\Request([], ['listview' => ['conditions' => '{}']]);

        $datagridBuilder = $this->getMockBuilder('Opifer\CrudBundle\Datagrid\DatagridBuilder')
            ->setMethods(array('getViewRepository', 'getRequest'))
            ->setConstructorArgs(array($this->container))
            ->getMock();
        $datagridBuilder->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));
        $datagridBuilder->expects($this->any())
            ->method('getViewRepository')
            ->will($this->returnValue($viewRepository));

        $datagrid = $datagridBuilder->create(new User())
            ->addColumn('name', new TextCell(), ['label' => 'Username'])
        ;

        $this->assertCount(1, $datagrid->getColumns());
        $first = $datagrid->getColumns()->first();

        $this->assertInstanceOf('Opifer\CrudBundle\Datagrid\Column\Column', $first);
        $this->assertEquals('name', $first->getProperty());
        $this->assertEquals(new TextCell(), $first->getCellType());
        $this->assertEquals('Username', $first->getLabel());
    }
}
