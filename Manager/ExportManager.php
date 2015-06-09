<?php

namespace Opifer\CrudBundle\Manager;

use JMS\Serializer\SerializerInterface;
use Liuggio\ExcelBundle\Factory;
use Opifer\CrudBundle\Datagrid\Datagrid;

class ExportManager
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var Factory
     */
    protected $phpExcel;

    /**
     * @param SerializerInterface $serializer
     * @param Factory             $phpExcel
     */
    public function __construct(SerializerInterface $serializer, Factory $phpExcel)
    {
        $this->serializer = $serializer;
        $this->phpExcel = $phpExcel;
    }

    /**
     * @param Datagrid $datagrid
     *
     * @throws \PHPExcel_Exception
     *
     * @return mixed
     */
    public function exportGrid(Datagrid $datagrid)
    {
        // @todo find a workaround
        set_time_limit(0);
        $phpExcelObject = $this->phpExcel->createPHPExcelObject();

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // Set the column headers
        $c = 'A';
        foreach ($columns = $datagrid->getColumns() as $column) {
            $phpExcelObject->getActiveSheet()->setCellValue($c.'1', $column->getLabel());

            $c++;
        }

        // Add the rows
        $r = 2;
        //while ($datagrid->getPaginator()->hasNextPage()) {
            $datagrid->getPaginator()->getNextPage();

            foreach ($datagrid->getRows() as $row) {
                $c = 'A';
                foreach ($row->getCells() as $cel) {
                    $phpExcelObject->getActiveSheet()->setCellValue($c.$r, $cel->getExportValue());

                    $c++;
                }

                $r++;
            }
        //}

        $phpExcelObject->getActiveSheet()->setTitle('Datagrid export');

        // create the writer
        $writer = $this->phpExcel->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->phpExcel->createStreamedResponse($writer);
        // adding headers
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=datagrid-export.xls');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }
}