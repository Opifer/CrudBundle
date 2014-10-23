<?php

namespace Opifer\CrudBundle\Tests\Datagrid;

use Opifer\CrudBundle\Datagrid\ViewBuilder;

class ViewBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $entityManager;

    private $entityHelper;

    public function __construct()
    {
        $this->entityManager = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityHelper = $this->getMockBuilder('Opifer\CrudBundle\Doctrine\EntityHelper')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Do we get the right array format?
     */
    public function testWheres()
    {
        $filterBuilder = new ViewBuilder($this->entityManager, $this->entityHelper);

        $input = array(
            array(
                'value' => '20',
                'param' => 'limit'
            ),
            array(
                'param' => 'ConditionClass-id',
                'comparator' => 'gte',
                'values' => '1'
            )
        );

        $expected = array(
            'where' => array('a.id','gte','1')
        );

        $this->assertEquals($expected, $filterBuilder->wheres($input));
    }
}
