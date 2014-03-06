<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_AbstractTest extends VF_TestCase
{

    function testShouldGetProductId()
    {
        $import = new VF_Import_AbstractTestSubClass($this->getServiceContainer()->getSchemaClass(
        ), $this->getServiceContainer()->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass());
        $expectedProductId = $this->insertProduct('sku');
        $this->assertEquals($expectedProductId, $import->productId('sku'));
    }

    function testRegression()
    {
        $import = new VF_Import_AbstractTestSubClass($this->getServiceContainer()->getSchemaClass(
        ), $this->getServiceContainer()->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass());
        $this->getReadAdapter()->query('update test_catalog_product_entity set sku=\'\' where 0');
        $expectedProductId = $this->insertProduct('sku');
        $this->assertEquals($expectedProductId, $import->productId('sku'));
    }
}