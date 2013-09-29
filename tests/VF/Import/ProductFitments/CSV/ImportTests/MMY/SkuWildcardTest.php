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
class VF_Import_ProductFitments_CSV_ImportTests_MMY_SkuWildcardTest extends VF_Import_ProductFitments_CSV_ImportTests_TestCase
{
    protected function doSetUp()
    {
        $this->switchSchema('make,model,year');
        $this->csvData = 'sku, make, model, year
sku*, honda, civic, 2000';

        $this->insertProduct('sku1');
        $this->insertProduct('sku2');
        $this->insertProduct('ZZZ');
    }

    function testShouldMatchSku1()
    {
        $this->mappingsImporterFromData($this->csvData)
            ->setProductTable('test_catalog_product_entity')
            ->import();
        $fit = $this->getFitForSku('sku1');
        $this->assertEquals('honda', $fit->getLevel('make')->getTitle());
    }

    function testShouldMatchSku2()
    {
        $this->mappingsImporterFromData($this->csvData)
            ->setProductTable('test_catalog_product_entity')
            ->import();
        $fit = $this->getFitForSku('sku2');
        $this->assertEquals('honda', $fit->getLevel('make')->getTitle());
    }


    function testShouldNotMatchZZZ()
    {
        $this->mappingsImporterFromData($this->csvData)
            ->setProductTable('test_catalog_product_entity')
            ->import();
        $fit = $this->getFitForSku('ZZZ');
        $this->assertFalse($fit);
    }

    function testShouldImportPrestaShop()
    {
        $this->query(sprintf("INSERT INTO `ps_product` ( `reference` ) values ( '%s' )", 'foobar123'));
        $productID1 = $this->getReadAdapter()->lastInsertId();
        $this->query(sprintf("INSERT INTO `ps_product` ( `reference` ) values ( '%s' )", 'foobar456'));
        $productID2 = $this->getReadAdapter()->lastInsertId();

        $this->mappingsImporterFromData('sku, make, model, year
foo*, honda, civic, 2000')
            ->setProductTable('ps_product')
            ->setProductSkuField('reference')
            ->setProductIdField('id_product')
            ->import();

        $product1 = new VF_Product;
        $product1->setId($productID1);

        $product2 = new VF_Product;
        $product2->setId($productID2);

        $fitments = $product1->getFitModels();
        $this->assertEquals('honda civic 2000', $fitments[0]->__toString(), 'should add fitment to product');

        $fitments = $product2->getFitModels();
        $this->assertEquals('honda civic 2000', $fitments[0]->__toString(), 'should add fitment to product');

    }

    function mappingsImporterFromFile($csvFile)
    {
        return new VF_Import_ProductFitments_CSV_Import($csvFile);
    }

}