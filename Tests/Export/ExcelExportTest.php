<?php

namespace Opifer\CrudBundle\Tests\Datagrid;

use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use Opifer\CrudBundle\Datagrid\Cell\Cell;
use Opifer\CrudBundle\Datagrid\Column\Column;
use Opifer\CrudBundle\Datagrid\Datagrid;
use Opifer\CrudBundle\Datagrid\Row\Row;
use Opifer\CrudBundle\Export\ExcelExport;
use PHPExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExportTest extends \PHPUnit_Framework_TestCase
{
    private $factory;

    public function setUp()
    {
        $this->factory = m::mock('Liuggio\ExcelBundle\Factory');
    }

    public function testCreateExportObject()
    {
        $this->factory->shouldReceive('createPHPExcelObject')->andReturn(new PHPExcel);

        $column = new Column();
        $column->setLabel('Label');

        $datagrid = new Datagrid();
        $datagrid->addColumn($column);

        $export = new ExcelExport($this->factory);
        $exportObject = $export->createExportObject($datagrid);

        $this->assertInstanceOf('PHPExcel', $exportObject);
        $this->assertEquals('Label', $exportObject->getActiveSheet()->getCell('A1')->getValue());
    }

    public function testWriteData()
    {
        $cell = new Cell();
        $cell->setExportValue('Export');

        $row = new Row();
        $row->addCell($cell);

        $rows = new ArrayCollection();
        $rows->add($row);

        $export = new ExcelExport($this->factory);
        $export->setPhpExcel(new PHPExcel());
        $export->writeData($rows);
        $exportObject = $export->getPhpExcel();

        $this->assertEquals('Export', $exportObject->getActiveSheet()->getCell('A2')->getValue());
    }

    public function testCreateResponse()
    {
        $writter = m::mock('PHPExcel_Writer_IWriter');
        $this->factory->shouldReceive('createWriter')->andReturn($writter);
        $this->factory->shouldReceive('createStreamedResponse')->andReturn(new StreamedResponse());

        $export = new ExcelExport($this->factory);
        $export->setPhpExcel(new PHPExcel());
        $response = $export->createResponse();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\StreamedResponse', $response);
    }
}
