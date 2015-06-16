<?php

namespace Opifer\CrudBundle\Export;

use Opifer\CrudBundle\Datagrid\Datagrid;
use Liuggio\ExcelBundle\Factory;
use PHPExcel;

class ExcelExport implements ExportInterface
{
    /** @var Factory */
    protected $factory;

    /** @var PHPExcel */
    protected $phpExcel;

    /**
     * ExcelExport constructor.
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @inheritdoc
     */
    public function createExportObject(Datagrid $datagrid)
    {
        $this->phpExcel = $this->factory->createPHPExcelObject();
        $this->phpExcel->setActiveSheetIndex(0);
        $this->phpExcel->getActiveSheet()->setTitle('Datagrid export');

        // Set the column headers
        $c = 'A';
        foreach ($columns = $datagrid->getColumns() as $column) {
            $this->phpExcel->getActiveSheet()->setCellValue($c.'1', $column->getLabel());

            $c++;
        }

        return $this->phpExcel;
    }

    /**
     * @inheritdoc
     */
    public function writeData($rows)
    {
        $r = intval($this->phpExcel->getActiveSheet()->getHighestDataRow()) + 1;

        foreach ($rows as $row) {
            $c = 'A';
            foreach ($row->getCells() as $cell) {
                $this->phpExcel->getActiveSheet()->setCellValue($c.$r, $cell->getExportValue());

                $c++;
            }

            $r++;
        }
    }

    /**
     * @inheritdoc
     */
    public function createResponse()
    {
        $writer = $this->factory->createWriter($this->phpExcel, 'Excel5');
        $response = $this->factory->createStreamedResponse($writer);
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=datagrid-export.xls');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }

    /**
     * @return PHPExcel
     */
    public function getPhpExcel()
    {
        return $this->phpExcel;
    }

    /**
     * @param PHPExcel $phpExcel
     */
    public function setPhpExcel($phpExcel)
    {
        $this->phpExcel = $phpExcel;
    }
}
