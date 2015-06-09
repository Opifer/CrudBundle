<?php

namespace Opifer\CrudBundle\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Liuggio\ExcelBundle\Factory;
use Opifer\CrudBundle\Datagrid\Datagrid;
use Opifer\CrudBundle\Datagrid\DatagridBuilderInterface;
use Opifer\CrudBundle\Datagrid\DatagridMapper;
use Opifer\CrudBundle\Datagrid\Grid\GridInterface;
use PHPExcel;

class ExportManager
{
    /** @var DatagridBuilderInterface */
    protected $builder;

    /** @var Factory */
    protected $phpExcel;

    /**
     * @param DatagridBuilderInterface $builder
     * @param Factory                  $phpExcel
     */
    public function __construct(DatagridBuilderInterface $builder, Factory $phpExcel)
    {
        $this->builder = $builder;
        $this->phpExcel = $phpExcel;
    }

    /**
     * @param GridInterface $grid
     * @param object        $data
     *
     * @throws \PHPExcel_Exception
     *
     * @return mixed
     *
     */
    public function exportGrid(GridInterface $grid, $data)
    {
        // @todo find a workaround
        set_time_limit(0);
        $datagrid = $this->prepareGrid($grid, $data);

        $excelObject = $this->phpExcel->createPHPExcelObject();
        $excelObject->setActiveSheetIndex(0);
        $excelObject->getActiveSheet()->setTitle('Datagrid export');

        // Set the column headers
        $c = 'A';
        foreach ($columns = $datagrid->getColumns() as $column) {
            $excelObject->getActiveSheet()->setCellValue($c.'1', $column->getLabel());

            $c++;
        }

        $paginator = $datagrid->getPaginator();
        if ($paginator) {
            for ($i = 1; $i <= $paginator->getNbPages(); $i++) {
                $datagrid = $this->prepareGrid($grid, $data, $i);
                $excelObject = $this->writeRows($excelObject, $datagrid->getRows());
            }
        } else {
            $excelObject = $this->writeRows($excelObject, $datagrid->getRows());
        }

        $writer = $this->phpExcel->createWriter($excelObject, 'Excel5');
        $response = $this->phpExcel->createStreamedResponse($writer);
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename=datagrid-export.xls');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }

    private function prepareGrid(GridInterface $grid, $data, $page = 1)
    {
        $builder = $this->builder->create($data);
        $builder->setLimit(1000);
        $builder->setPage($page);

        $grid->buildGrid($builder);
        $datagrid = $builder->build();

        return $datagrid;
    }

    /**
     * @param PHPExcel        $excelObject
     * @param ArrayCollection $rows
     *
     * @return PHPExcel
     */
    private function writeRows(PHPExcel $excelObject, $rows)
    {
        $r = intval($excelObject->getActiveSheet()->getHighestDataRow()) + 1;

        foreach ($rows as $row) {
            $c = 'A';
            foreach ($row->getCells() as $cel) {
                $excelObject->getActiveSheet()->setCellValue($c.$r, $cel->getExportValue());

                $c++;
            }

            $r++;
        }

        return $excelObject;
    }
}