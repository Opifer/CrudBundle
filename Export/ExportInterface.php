<?php

namespace Opifer\CrudBundle\Export;

use Doctrine\Common\Collections\ArrayCollection;
use Opifer\CrudBundle\Datagrid\Datagrid;

interface ExportInterface
{
    /**
     * Creates an export object
     *
     * @param Datagrid $datagrid
     */
    public function createExportObject(Datagrid $datagrid);

    /**
     * Writes the datagrid rows to the export object
     *
     * @param ArrayCollection|null $rows
     */
    public function writeData($rows);

    /**
     * Creates a response from the export object
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function createResponse();
}