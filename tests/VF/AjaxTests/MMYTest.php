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
class VF_AjaxTests_MMYTest extends VF_AjaxTests_AjaxTestCase
{

    function testShouldListMakes()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');
        $this->insertMappingMMY($vehicle);
        $_GET['requestLevel'] = 'make';
        $this->assertEquals('<option value="Honda">Honda</option>', $this->execute(), 'should list makes');
    }

    function testShouldListModels()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');
        $this->insertMappingMMY($vehicle);
        $_GET['make'] = 'Honda';
        $_GET['requestLevel'] = 'model';
        $this->assertEquals('<option value="Civic">Civic</option>', $this->execute(), 'should list models for a make');
    }

    function testShouldListYears()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');
        $this->insertMappingMMY($vehicle);
        $_GET['make'] = 'Honda';
        $_GET['model'] = 'Civic';
        $_GET['requestLevel'] = 'year';
        $this->assertEquals('<option value="2000">2000</option>', $this->execute(), 'should list years for a model');
    }

    function testShouldListYearsInUse()
    {
        $this->createMMY('Honda', 'Civic', '2001');
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');
        $this->insertMappingMMY($vehicle);
        $_GET['make'] = 'Honda';
        $_GET['model'] = 'Civic';
        $_GET['requestLevel'] = 'year';
        $this->assertEquals('<option value="2000">2000</option>', $this->execute(), 'should list years for a model');
        // @todo assert regexp not contains "2001"
    }

    function testShouldListDistinctModelsWhenMultipleYears()
    {
        $vehicle1 = $this->createMMY('Honda', 'Civic', '2000');
        $vehicle2 = $this->createMMY('Honda', 'Civic', '2001');
        $this->insertMappingMMY($vehicle1);
        $this->insertMappingMMY($vehicle2);
        $_GET['make'] = 'Honda';
        $_GET['requestLevel'] = 'model';
        $this->assertEquals('<option value="Civic">Civic</option>', $this->execute(), 'should list models for a make');
    }

    function testShouldSortMake()
    {
        return $this->markTestIncomplete();
    }

    function testShouldSortModels()
    {
        return $this->markTestIncomplete();
    }

    function testShouldListMultipleModels()
    {
        $vehicle1 = $this->createMMY('Honda', 'Accord', '2000');
        $vehicle2 = $this->createMMY('Honda', 'Civic', '2001');
        $this->insertMappingMMY($vehicle1);
        $this->insertMappingMMY($vehicle2);
        $_GET['make'] = 'Honda';
        $_GET['requestLevel'] = 'model';
        $this->assertEquals('<option value="0">-please select-</option><option value="Accord">Accord</option><option value="Civic">Civic</option>', $this->execute(), 'should list models for a make');
    }

    function testShouldNotListModelsNotInUse()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2001');
        $_GET['make'] = 'Honda';
        $_GET['requestLevel'] = 'model';
        $this->assertEquals('', $this->execute(), 'should not list models not in use');
    }

    function testShouldListModelsNotInUseIfConfigSaysTo()
    {
        $vehicle = $this->createVehicle(array('make' => 'Honda', 'model' => 'Civic', 'year' => 2000));
        $_GET['make'] = 'Honda';
        $_GET['requestLevel'] = 'model';
        ob_start();
        $_GET['front'] = 1;
        $config = new Zend_Config(array('search' => array('showAllOptions' => 'true')));
        $ajax = $this->getAjax();
        $ajax->setConfig($config);
        $ajax->execute();
        $actual = ob_get_clean();
        $expected = '<option value="Civic">Civic</option>';
        $this->assertEquals($expected, $actual, 'should list models not in use if config says to');
    }

    function testShouldListMultipleModels_WithDefaultOption()
    {
        $vehicle1 = $this->createMMY('Honda', 'Accord', '2000');
        $vehicle2 = $this->createMMY('Honda', 'Civic', '2001');
        $this->insertMappingMMY($vehicle1);
        $this->insertMappingMMY($vehicle2);
        $_GET['make'] = 'Honda';
        $_GET['requestLevel'] = 'model';
        $_GET['front'] = true;
        $this->assertEquals('<option value="0">-please select-</option><option value="Accord">Accord</option><option value="Civic">Civic</option>', $this->execute(), 'should list models for a make');
    }

    function testShouldListMultipleModels_WithCustomDefaultOption()
    {
        $vehicle1 = $this->createMMY('Honda', 'Accord', '2000');
        $vehicle2 = $this->createMMY('Honda', 'Civic', '2001');
        $this->insertMappingMMY($vehicle1);
        $this->insertMappingMMY($vehicle2);
        $_GET['make'] = 'Honda';
        $_GET['requestLevel'] = 'model';
        $_GET['front'] = true;
        $ajax = $this->getAjax();
        $config = new Zend_Config(array('search' => array('defaultText' => '-All %s-')));
        $ajax->setConfig($config);
        ob_start();
        $ajax->execute();
        $actual = ob_get_clean();
        $expected = '<option value="0">-All Model-</option><option value="Accord">Accord</option><option value="Civic">Civic</option>';
        $this->assertEquals($expected, $actual, 'should list models for a make');
    }

    function testShouldListMultipleYears()
    {
        $vehicle1 = $this->createMMY('Honda', 'Civic', '2000');
        $vehicle2 = $this->createMMY('Honda', 'Civic', '2001');
        $vehicle3 = $this->createMMY('Honda', 'Civic', '2002');
        $this->insertMappingMMY($vehicle1);
        $this->insertMappingMMY($vehicle2);
        $this->insertMappingMMY($vehicle3);
        $_GET['make'] = 'Honda';
        $_GET['model'] = 'Civic';
        $_GET['requestLevel'] = 'year';
        $this->assertEquals('<option value="0">-please select-</option><option value="2000">2000</option><option value="2001">2001</option><option value="2002">2002</option>', $this->execute(), 'should list models for a make');
    }

}