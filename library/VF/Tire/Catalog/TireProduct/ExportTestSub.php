<?php

class VF_Tire_Catalog_TireProduct_ExportTestSub extends VF_Tire_Catalog_TireProduct_Export
{

    function getProductTable()
    {
        return 'test_catalog_product_entity';
    }
}