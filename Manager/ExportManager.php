<?php

namespace Opifer\CrudBundle\Manager;

use Opifer\CrudBundle\Datagrid\Datagrid;
use Opifer\CrudBundle\Datagrid\DatagridBuilderInterface;
use Opifer\CrudBundle\Datagrid\Grid\GridInterface;
use Opifer\CrudBundle\Export\ExportInterface;

class ExportManager
{
    /** @var DatagridBuilderInterface */
    protected $builder;

    /** @var ExportInterface */
    protected $exportStrategy;

    /**
     * @param DatagridBuilderInterface $builder
     */
    public function __construct(DatagridBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param GridInterface $grid
     * @param object        $data
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportGrid(GridInterface $grid, $data)
    {
        // @todo find a workaround
        set_time_limit(0);
        $datagrid = $this->prepareGrid($grid, $data);
        $this->exportStrategy->createExportObject($datagrid);

        $paginator = $datagrid->getPaginator();
        if ($paginator) {
            for ($i = 1; $i <= $paginator->getNbPages(); $i++) {
                $datagrid = $this->prepareGrid($grid, $data, $i);
                $this->exportStrategy->writeData($datagrid->getRows());
            }
        } else {
            $this->exportStrategy->writeData($datagrid->getRows());
        }

        return $this->exportStrategy->createResponse();
    }

    /**
     * @param GridInterface $grid
     * @param object        $data
     * @param int           $page
     *
     * @return Datagrid
     */
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
     * @return ExportInterface
     */
    public function getExportStrategy()
    {
        return $this->exportStrategy;
    }

    /**
     * @param ExportInterface $exportStrategy
     */
    public function setExportStrategy($exportStrategy)
    {
        $this->exportStrategy = $exportStrategy;
    }
}
