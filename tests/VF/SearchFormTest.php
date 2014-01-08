<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 *
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_SearchForm_Search_SearchFormTest extends VF_TestCase
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
        $this->assertEquals(
            $vehicle->getLevel('make')->getTitle(),
            $actual[0]->getTitle(),
            'should list makes when no vehicle is selected'
        );
    }

    function test_MMYShouldNotListModelsBeforeMakeIsSelected()
    {
        $this->switchSchema('make,model,year');
        $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $search = new VF_SearchForm();
        VF_Singleton::getInstance()->setRequest($this->getRequest());
        $actual = $search->listEntities('model');
        $this->assertEquals(array(), $actual, 'should not list models before make is selected');
    }

    function test_MMYShouldListModels_WhenVehicleIsSelected()
    {
        $this->switchSchema('make,model,year');
        $vehicle = $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $search = new VF_SearchForm;
        VF_Singleton::getInstance()->setRequest($this->getRequest($vehicle->toTitleArray()));
        $actual = $search->listEntities('model');
        $this->assertEquals(1, count($actual));
        $this->assertEquals('Civic', $actual[0]->getTitle(), 'should list models when make is selected');
    }

    /**
     * Should throw exception when asked to list blank level
     *
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
     *
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
        VF_Singleton::getInstance()->setRequest($this->getRequest());
        $actual = $search->listEntities('model');
        $this->assertEquals(array(), $actual, 'should not list models before make is selected');
    }

    function test_MMYShouldListModels_WhenPartialVehicleIsSelected()
    {
        $this->switchSchema('make,model,year');
        $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $_GET['make'] = 'Honda';
        $search = new VF_SearchForm();
        VF_Singleton::getInstance()->setRequest($this->getRequest());
        $actual = $search->listEntities('model');
        $this->assertEquals(1, count($actual), 'should list models when just make is selected');
        $this->assertEquals('Civic', $actual[0]->getTitle(), 'should list models when just make is selected');
    }

    function test_YMMShouldListYearsInUse()
    {
        $this->switchSchema('year,make,model');
        $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $search = new VF_SearchForm();
        VF_Singleton::getInstance()->setRequest($this->getRequest());
        $actual = $search->listEntities('year', '');
        $this->assertEquals(1, count($actual));
        $this->assertEquals('2000', $actual[0]->getTitle(), 'should list years when year not yet selected');
    }

    function test_YMMShouldListMakesInUse()
    {
        $this->switchSchema('year,make,model');
        $vehicle = $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $search = new VF_SearchForm();
        VF_Singleton::getInstance()->setRequest($this->getRequest());
        $request = $this->getRequest($vehicle->toTitleArray());
        VF_Singleton::getInstance()->setRequest($request);
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
        VF_Singleton::getInstance()->setRequest($this->getRequest());
        $request = $this->getRequest($vehicle->toTitleArray());
        VF_Singleton::getInstance()->setRequest($request);
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
        VF_Singleton::getInstance()->setRequest($this->getRequest());
        $request = $this->getRequest($vehicle->toTitleArray());
        VF_Singleton::getInstance()->setRequest($request);
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
        VF_Singleton::getInstance()->setRequest($this->getRequest());
        $request = $this->getRequest($vehicle->toTitleArray());
        VF_Singleton::getInstance()->setRequest($request);
        $this->setRequest($request);
        $actual = $search->listEntities('make');
        $this->assertEquals(1, count($actual), 'should list makes not in use when config says to');
    }

    function test_YMMListModel()
    {
        $this->switchSchema('year,make,model');
        $vehicle = $this->createMMYWithFitment('Honda', 'Civic', '2000');
        $search = new VF_SearchForm();
        VF_Singleton::getInstance()->setRequest($this->getRequest($vehicle->toTitleArray()));
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
        VF_Singleton::getInstance()->setRequest($request);
        $this->assertEquals($vehicle->getValue('model'), $search->getSelected('model'));
    }   
    

    public function testWhereListEntitiesFiltersRequestParamsUsingOnlyYear()
    {
        $this->switchSchema('make,model,year,engine');

        $chevroletTahoe = $this->createVehicle(
            array('make' => 'Chevrolet', 'model' => 'Tahoe', 'year' => '2002', 'engine' => '5.3L V8')
        );
        $toyotaCamry = $this->createVehicle(
            array('make' => 'Toyota', 'model' => 'Camry', 'year' => '2002', 'engine' => '2.4L 2AZ-FE')
        );
        $chevyAstro = $this->createVehicle(
            array('make' => 'Chevrolet', 'model' => 'Astro Van', 'year' => '2001', 'engine' => '4.3L V6')
        );

        $this->insertMapping($chevroletTahoe, 1);
        $this->insertMapping($toyotaCamry, 2);
        $this->insertMapping($chevyAstro, 5);

        $request = new Zend_Controller_Request_Http();
        $request->setParam('year', $chevroletTahoe->getLevel('year')->getId());

        $searchForm = new VF_SearchForm;
        VF_Singleton::getInstance()->setRequest($request);
        $entities = $searchForm->listEntities('engine');

        $this->assertArrayDoesNotHaveLevelIDPresent($entities, $chevyAstro->getLevel('engine'));
        $this->assertArrayShouldContainLevelId($entities, $chevroletTahoe->getLevel('engine'));
        $this->assertArrayShouldContainLevelId($entities, $toyotaCamry->getLevel('engine'));

    }

    public function testWhereListEntitiesFiltersRequestParamsToOnlyDisplaySingleEntity()
    {
        $this->switchSchema('make,model,year,engine');

        $chevroletTahoe = $this->createVehicle(
            array('make' => 'Chevrolet', 'model' => 'Tahoe', 'year' => '2002', 'engine' => '5.3L V8')
        );
        $toyotaCamry = $this->createVehicle(
            array('make' => 'Toyota', 'model' => 'Camry', 'year' => '2002', 'engine' => '2.4L 2AZ-FE')
        );
        $chevyAstro = $this->createVehicle(
            array('make' => 'Chevrolet', 'model' => 'Astro Van', 'year' => '2001', 'engine' => '4.3L V6')
        );

        $this->insertMapping($chevroletTahoe, 1);
        $this->insertMapping($toyotaCamry, 2);
        $this->insertMapping($chevyAstro, 5);

        $request = new Zend_Controller_Request_Http();
        $request->setParam('make', $chevroletTahoe->getLevel('make')->getId());
        $request->setParam('model', $chevroletTahoe->getLevel('model')->getId());
        $request->setParam('year', $chevroletTahoe->getLevel('year')->getId());

        $searchForm = new VF_SearchForm;
        VF_Singleton::getInstance()->setRequest($request);
        $entities = $searchForm->listEntities('engine');

        $this->assertEquals(1,count($entities));
        $this->assertArrayOnlyHasLevelIDPresent($entities, $chevroletTahoe->getLevel('engine'));
    }


    public function assertArrayOnlyHasLevelIDPresent(array $levels, VF_Level $actualLevel)
    {
        foreach ($levels AS $level) {
            /** @var $level VF_Level */
            if ($level->getId() != $actualLevel->getId()) {
                $this->fail(
                    sprintf("Level ID %s was not supposed to be in result but was found.", $actualLevel->getId())
                );
            }
        }
        return;
    }

    public function assertArrayShouldContainLevelId(array $levels, VF_Level $actualLevel)
    {
        foreach ($levels AS $level) {
            /** @var $level VF_Level */
            if ($level->getId() == $actualLevel->getId()) {
                return;
            }
        }
        $this->fail(
            sprintf("Level ID %s was supposed to be in result at least once but was found.", $actualLevel->getId())
        );
    }


    public function assertArrayDoesNotHaveLevelIDPresent(array $levels, VF_Level $actualLevel)
    {
        foreach ($levels AS $level) {
            /** @var $level VF_Level */
            if ($level->getId() == $actualLevel->getId()) {
                $this->fail(
                    sprintf("Level ID %s was not supposed to be in result but was found.", $actualLevel->getId())
                );
            }
        }
        return;
    }

}