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
class VF_SearchForm_Search_ListEntitiesMMYAlnumTest extends VF_TestCase
{
    function doSetUp()
    {
    }
    
    function test_MMYShouldListMakes_WhenNoVehicleIsSelected()
    {
        $this->switchSchema('make,model,year');
        $vehicle = $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $search = new VF_SearchForm;
        $actual = $search->listEntities('make');
        $this->assertEquals(1, count($actual), 'should list makes when no vehicle is selected');
        $this->assertEquals($vehicle->getLevel('make')->getTitle(), $actual[0]->getTitle(), 'should list makes when no vehicle is selected');
    }

    function test_MMYShouldNotListModelsBeforeMakeIsSelected()
    {
        $this->switchSchema('make,model,year');
        $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $search = new VF_SearchForm();
        $search->setRequest($this->getRequest());
        $actual = $search->listEntities('model');
        $this->assertEquals(array(), $actual, 'should not list models before make is selected');
    }

    function test_MMYShouldListModels_WhenVehicleIsSelected()
    {
        $this->switchSchema('make,model,year');
        $vehicle = $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $search = new VF_SearchForm;
        $search->setRequest($this->getRequest($vehicle->toTitleArray()));
        $actual = $search->listEntities('model');
        $this->assertEquals(1, count($actual));
        $this->assertEquals('Civic', $actual[0]->getTitle(), 'should list models when make is selected');
    }

    /**
     * Should throw exception when asked to list blank level
     * @expectedException VF_Level_Exception_InvalidLevel
     */
    function test_MMYShouldThrowExceptionWhenAskedToListBlankLevel()
    {
        $this->switchSchema('make,model,year');
        $search = new VF_SearchForm();
        $search->listEntities('');
    }

    /**
     * Should throw exception when asked to list invalid level
     * @expectedException VF_Level_Exception_InvalidLevel
     */
    function test_MMYShouldThrowExceptionWhenAskedToListInvalidLevel()
    {
        $this->switchSchema('make,model,year');
        $search = new VF_SearchForm();
        $search->listEntities('foo');
    }

    function test_MMYShouldBeNoModelsPreselected_WhenNoVehicleIsSelected()
    {
        $this->switchSchema('make,model,year');
        $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $search = new VF_SearchForm();
        $search->setRequest($this->getRequest());
        $actual = $search->listEntities('model');
        $this->assertEquals(array(), $actual, 'should not list models before make is selected');
    }

    function test_MMYShouldListModels_WhenPartialVehicleIsSelected()
    {
        $this->switchSchema('make,model,year');
        $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $_GET['make'] = 'Honda';
        $search = new VF_SearchForm();
        $search->setRequest($this->getRequest());
        $actual = $search->listEntities('model');
        $this->assertEquals(1, count($actual), 'should list models when just make is selected');
        $this->assertEquals('Civic', $actual[0]->getTitle(), 'should list models when just make is selected');
    }

    function test_YMMShouldListYearsInUse()
    {
        $this->switchSchema('year,make,model');
        $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $search = new VF_SearchForm();
        $search->setRequest($this->getRequest());
        $actual = $search->listEntities('year', '');
        $this->assertEquals(1, count($actual));
        $this->assertEquals('2000', $actual[0]->getTitle(), 'should list years when year not yet selected');
    }

    function test_YMMShouldListMakesInUse()
    {
        $this->switchSchema('year,make,model');
        $vehicle = $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $search = new VF_SearchForm();
        $search->setRequest($this->getRequest());
        $request = $this->getRequest($vehicle->toTitleArray());
        $search->setRequest($request);
        $this->setRequest($request);
        $actual = $search->listEntities('make');
        $this->assertEquals(1, count($actual));
        $this->assertEquals('Honda', $actual[0]->getTitle(), 'should list makes in use when model is selected');
    }

    function test_YMMShouldNotListMakesNotInUse()
    {
        $this->switchSchema('year,make,model');
        $vehicle = $this->createVehicle(array('make' => 'Honda', 'model' => 'Civic', 'year' => 2000));
        $search = new VF_SearchForm();
        $search->setRequest($this->getRequest());
        $request = $this->getRequest($vehicle->toTitleArray());
        $search->setRequest($request);
        $this->setRequest($request);
        $actual = $search->listEntities('make');
        $this->assertEquals(0, count($actual), 'should not list makes not in use when model is selected');
    }

    function test_YMMShouldListYearsNotInUseIfConfigSaysTo()
    {
        $this->switchSchema('year,make,model');
        $config = new Zend_Config(array('search' => array('showAllOptions' => 'true')));
        $vehicle = $this->createVehicle(array('make' => 'Honda', 'model' => 'Civic', 'year' => 2000));
        $search = new VF_SearchForm();
        $search->setConfig($config);
        $search->setRequest($this->getRequest());
        $request = $this->getRequest($vehicle->toTitleArray());
        $search->setRequest($request);
        $this->setRequest($request);
        $actual = $search->listEntities('year');
        $this->assertEquals(1, count($actual), 'should list years not in use when config says to');
    }

    function test_YMMShouldListMakesNotInUseIfConfigSaysTo()
    {
        $this->switchSchema('year,make,model');
        $config = new Zend_Config(array('search' => array('showAllOptions' => 'true')));
        $vehicle = $this->createVehicle(array('make' => 'Honda', 'model' => 'Civic', 'year' => 2000));
        $search = new VF_SearchForm();
        $search->setConfig($config);
        $search->setRequest($this->getRequest());
        $request = $this->getRequest($vehicle->toTitleArray());
        $search->setRequest($request);
        $this->setRequest($request);
        $actual = $search->listEntities('make');
        $this->assertEquals(1, count($actual), 'should list makes not in use when config says to');
    }

    function test_YMMListModel()
    {
        $this->switchSchema('year,make,model');
        $vehicle = $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $search = new VF_SearchForm();
        $search->setRequest($this->getRequest($vehicle->toTitleArray()));
        $actual = $search->listEntities('model');
        $this->assertEquals(1, count($actual));
        $this->assertEquals('Civic', $actual[0]->getTitle(), 'should list models when make is selected');
    }

    function testSelected()
    {
        $this->switchSchema('make,model,year');
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');
        $request = new Zend_Controller_Request_Http;
        $request->setParams($vehicle->toTitleArray());
        $search = new VF_SearchForm;
        $search->setRequest($request);
        $this->assertEquals($vehicle->getValue('model'), $search->getSelected('model'));
    }
}