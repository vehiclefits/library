<?php
/**
 * Vehicle Fits
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@vehiclefits.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Vehicle Fits to newer
 * versions in the future. If you wish to customize Vehicle Fits for your
 * needs please refer to http://www.vehiclefits.com for more information.
 * @copyright  Copyright (c) 2013 Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_ProductFitments_CSV_ImportTests_MMY_SimpleTest extends VF_Import_ProductFitments_CSV_ImportTests_TestCase
{
    protected function doSetUp()
    {
        $this->switchSchema('make,model,year');
        $this->csvData = 'sku, make, model, year
sku, honda, civic, 2000';

        $this->query(sprintf("INSERT INTO test_catalog_product_entity ( `sku` ) values ( '%s' )", self::SKU));
    }

    function testShouldAddFitmentToProduct()
    {
        $this->mappingsImporterFromData($this->csvData)
            ->setProductTable('test_catalog_product_entity')
            ->import();

        $product = $this->getVFProductForSku(self::SKU);
        $fitments = $product->getFitModels();
        $this->assertEquals('honda civic 2000', $fitments[0]->__toString(), 'should add fitment to product');
    }

    function testShouldCreateVehicle()
    {
        $this->mappingsImporterFromData($this->csvData)
            ->setProductTable('test_catalog_product_entity')
            ->import();
        $vehicleExists = $this->vehicleExists(array(
            'make' => 'honda',
            'model'=> 'civic',
            'year' => '2000'
        ));
        $this->assertTrue($vehicleExists, 'should create vehicle');
    }

    function testCountMappingsIs1AfterSuccess()
    {
        $importer = $this->mappingsImporterFromData($this->csvData)
            ->setProductTable('test_catalog_product_entity')
            ->import();
        $this->assertEquals(1, $importer->getCountMappings(), 'should report on statistics that 1 fitment was imported');
    }

    function testAddedCountIs0IfFitAlreadyExists()
    {
        $importer = $this->mappingsImporterFromData($this->csvData)
            ->setProductTable('test_catalog_product_entity')
            ->import()
            ->import();

        $this->assertEquals(0, $importer->getCountMappings(), 'shouldn\'t report on statistics for already existing vehicles');
    }

    function testShouldImportPrestaShop()
    {
        $this->query(sprintf("INSERT INTO `ps_product` ( `reference` ) values ( '%s' )", 'foobar123'));
        $productID = $this->getReadAdapter()->lastInsertId();

        $this->mappingsImporterFromData('sku, make, model, year
foobar123, honda, civic, 2000')
            ->setProductTable('ps_product')
            ->setProductSkuField('reference')
            ->setProductIdField('id_product')
            ->import();

        $product = new VF_Product;
        $product->setId($productID);

        $fitments = $product->getFitModels();
        $this->assertEquals('honda civic 2000', $fitments[0]->__toString(), 'should add fitment to product');

    }

    function mappingsImporterFromFile($csvFile)
    {
        return new VF_Import_ProductFitments_CSV_Import($csvFile);
    }

}