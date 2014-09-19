<?php

namespace Opifer\CrudBundle\Tests\Datagrid;

use Opifer\CrudBundle\Datagrid\Datagrid;

class DatagridTest extends \PHPUnit_Framework_TestCase
{
    private $requestStack;

    private $entityManager;

    private $filterBuilder;

    public function __construct()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');

        $this->requestStack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack');
        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->will($this->returnValue($request));

        $this->entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterBuilder = $this->getMockBuilder('Opifer\CrudBundle\Datagrid\FilterBuilder')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Are the properties 'entity' and 'options' set after buildGrid()?
     */
    public function testInitialize()
    {
        $datagrid = new Datagrid($this->requestStack, $this->entityManager, $this->filterBuilder);
        $datagrid = $datagrid->init('entitystring');

        $this->assertInternalType('string', $datagrid->getEntity());
        $this->assertInternalType('array', $datagrid->getOptions());
    }

    /**
     * Are the columns transformed properly?
     */
    public function testSetColumns()
    {
        $datagrid = new Datagrid($this->requestStack, $this->entityManager, $this->filterBuilder);

        $input = array(
            array(
                'property' => 'title',
                'type' => 'string'
            ),
            array(
                'property' => 'createdAt',
                'type' => 'datetime'
            )
        );
        $expected = array(
            array(
                'label' => 'Title',
                'property' => 'title',
                'type' => 'string'
            ), array(
                'label' => 'Created At',
                'property' => 'createdAt',
                'type' => 'datetime'
            )
        );

        $datagrid = $datagrid->setColumns($input, false);

        $this->assertEquals($expected, $datagrid->getColumns());
    }
}
