<?php

class VF_Import_ProductFitmentsExport_TestStub extends VF_Import_ProductFitments_CSV_Export
{
    public function __construct(VF_Schema $schema, Zend_Db_Adapter_Abstract $adapter)
    {
        $this->schema = $schema;
        $this->readAdapter = $adapter;
    }

    function getProductTable()
    {
        return 'test_catalog_product_entity';
    }
}