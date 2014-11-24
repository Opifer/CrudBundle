<?php

namespace Opifer\CrudBundle\Tests\Datagrid;

use Doctrine\Common\Collections\ArrayCollection;


use Opifer\CrudBundle\Datagrid\DatagridMapper;
use Opifer\CrudBundle\Datagrid\Column\Column;
use Opifer\CrudBundle\Datagrid\Cell\Type\TextCell;
use Opifer\CrudBundle\Datagrid\Cell\Cell;
use Opifer\CrudBundle\Datagrid\Row\Row;
use Opifer\CrudBundle\Tests\TestData\User;

class DatagridMapperTest extends \PHPUnit_Framework_TestCase
{
    public function testMapDefinedColumns()
    {
        $column = new Column();
        $column->setProperty('name');
        $column->setCellType(new TextCell());
        $column->setLabel('User name');

        $collection = new ArrayCollection();
        $collection->add($column);

        $mapper = new DatagridMapper();
        $actual = $mapper->mapColumns($collection);

        $this->assertSame($collection, $actual);
    }

    public function testMapColumns()
    {
        $input = array(array(
            'property' => 'name',
            'type' => 'text',
            'options' => array(
                'label' => 'User name'
            )
        ));

        $mapper = new DatagridMapper();
        $actual = $mapper->mapColumns($input);

        $column = new Column();
        $column->setProperty('name');
        $column->setCellType(new TextCell());
        $column->setLabel('User name');

        $expected = new ArrayCollection();
        $expected->add($column);

        $this->assertEquals($expected, $actual);
    }

    public function testMapRows()
    {
        $column = new Column();
        $column->setProperty('name');
        $column->setCellType(new TextCell());
        $column->setLabel('User name');
        $columnCollection = new ArrayCollection();
        $columnCollection->add($column);

        $user = new User();
        $user->setId(1);
        $user->setName('some random name');
        $user->setEmail('info@email.com');
        $rowCollection = new ArrayCollection();
        $rowCollection->add($user);

        $mapper = new DatagridMapper();
        $actual = $mapper->mapRows($rowCollection, $columnCollection);

        $cell = new Cell();
        $cell->setProperty('name');
        $cell->setValue('some random name');
        $cell->setType('text');
        $cell->setView('text');
        $cell->setAttributes([]);

        $row = new Row();
        $row->setId(1);
        $row->setName(1);
        $row->setObject($user);
        $row->addCell($cell);

        $expected = new ArrayCollection();
        $expected->add($row);

        $this->assertEquals($actual, $expected);
    }
}
