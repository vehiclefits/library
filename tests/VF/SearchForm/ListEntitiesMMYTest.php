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
class VF_SearchForm_Search_ListEntitiesMMYTest extends VF_TestCase
{
    function doSetUp()
    {
    }

    /**
     * Should throw exception when asked to list blank level
     * @expectedException VF_Level_Exception_InvalidLevel
     */
    function testShouldThrowExceptionWhenAskedToListBlankLevel()
    {
        $this->switchSchema('make,model,year');
        $search = new VF_SearchForm();
        $search->listEntities('');
    }

    /**
     * Should throw exception when asked to list invalid level
     * @expectedException VF_Level_Exception_InvalidLevel
     */
    function testShouldThrowExceptionWhenAskedToListInvalidLevel()
    {
        $this->switchSchema('make,model,year');
        $search = new VF_SearchForm();
        $search->listEntities('foo');
    }

    function testShouldListMakes_WhenNoVehicleIsSelected()
    {
        $this->switchSchema('make,model,year');
        $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $search = new VF_SearchForm();
        $actual = $search->listEntities('make');
        $this->assertEquals(1, count($actual), 'should list make when no vehicle selected');
        $this->assertEquals('Honda', $actual[0]->getTitle(), 'should list make when no vehicle selected');
    }

    function testShouldBeNoModelsPreselected_WhenNoVehicleIsSelected()
    {
        $this->switchSchema('make,model,year');
        $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $search = new VF_SearchForm();
        $search->setRequest($this->getRequest());
        $actual = $search->listEntities('model');
        $this->assertEquals(array(), $actual, 'should not list models before make is selected');
    }

    function testShouldListModels_WhenVehicleIsSelected()
    {
        $this->switchSchema('make,model,year');
        $vehicle = $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $_GET = array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2000',
        );
        $search = new VF_SearchForm();
        $search->setRequest($this->getRequest());
        $actual = $search->listEntities('model');
        $this->assertEquals(1, count($actual), 'should list models when make is selected');
        $this->assertEquals('Civic', $actual[0]->getTitle(), 'should list models when make is selected');
    }

    function testShouldListModels_WhenPartialVehicleIsSelected()
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
}