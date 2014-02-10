<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_FlexibleSearchTests_FitMultipleSelectionTest extends VF_TestCase
{
    protected function doSetUp()
    {
        $this->switchSchema('make,model,year');
    }

    function testShouldDetectNumericRequest()
    {
        $civic2000 = $this->createMMY('Honda', 'Civic', '2000');
        $civic2001 = $this->createMMY('Honda', 'Civic', '2001');
        $requestParams = array(
            'make' => $civic2000->getValue('make'),
            'model' => $civic2000->getValue('model'),
            'year_start' => $civic2000->getValue('year'),
            'year_end' => $civic2001->getValue('year')
        );
        $helper = $this->getHelper(array(), $requestParams);
        $this->assertTrue($helper->flexibleSearch()->isNumericRequest());
    }

    function testShouldHaveMake()
    {
        $civic2000 = $this->createMMY('Honda', 'Civic', '2000');
        $civic2001 = $this->createMMY('Honda', 'Civic', '2001');
        $requestParams = array(
            'make' => $civic2000->getValue('make'),
            'model' => $civic2000->getValue('model'),
            'year_start' => $civic2000->getValue('year'),
            'year_end' => $civic2001->getValue('year')
        );
        $helper = $this->getHelper(array(), $requestParams);
        $this->assertEquals($civic2000->getValue('make'), $helper->flexibleSearch()->getValueForSelectedLevel('make'));
    }

    function testShouldFitInsideRange()
    {
        $civic2000 = $this->createMMY('Honda', 'Civic', '2000');
        $civic2001 = $this->createMMY('Honda', 'Civic', '2001');
        $requestParams = array(
            'make' => $civic2000->getValue('make'),
            'model' => $civic2000->getValue('model'),
            'year_start' => $civic2000->getValue('year'),
            'year_end' => $civic2001->getValue('year')
        );
        $helper = $this->getHelper(array(), $requestParams);
        $this->assertEquals(2, count($helper->vehicleSelection()));
    }

    function testShouldNotFitOutsideRange()
    {
        $civic2000 = $this->createMMY('Honda', 'Civic', '2000');
        $civic2001 = $this->createMMY('Honda', 'Civic', '2001');
        $requestParams = array(
            'make' => $civic2000->getValue('make'),
            'model' => $civic2000->getValue('model'),
            'year_start' => $civic2001->getValue('year'),
            'year_end' => $civic2001->getValue('year')
        );
        $helper = $this->getHelper(array(), $requestParams);
        $this->assertEquals(1, count($helper->vehicleSelection()));
    }

    function testShouldStoreInSession()
    {
        $_SESSION = array('make' => null, 'model' => null, 'year' => null, 'year_start' => null, 'year_end' => null);
        $civic2000 = $this->createMMY('Honda', 'Civic', '2000');
        $civic2001 = $this->createMMY('Honda', 'Civic', '2001');
        $requestParams = array(
            'make' => $civic2000->getValue('make'),
            'model' => $civic2000->getValue('model'),
            'year_start' => $civic2000->getValue('year'),
            'year_end' => $civic2001->getValue('year')
        );
        $helper = $this->getHelper(array(), $requestParams);
        $helper->storeFitInSession();
        unset($_SESSION['garage']);
        $this->assertNull($_SESSION['year'], 'should store vehicle in session');
        $this->assertEquals($civic2000->getValue('year'), $_SESSION['year_start'], 'should store vehicle in session');
        $this->assertEquals($civic2001->getValue('year'), $_SESSION['year_end'], 'should store vehicle in session');
    }

    function testShouldReturnMappedEntityIdsForRequestedVehicleWithUniversalMapping() {
        $civic2000 = $this->createVehicle(array('make'=>'Honda','model'=>'Civic','year'=>'2000'));
        $this->insertMappingsForVehicle($civic2000,array(1,3,7));
        $this->insertUniversalFit(1000);

        $requestParams = array();
        $helper = $this->getHelper(array(),$requestParams);
        $helper->storeFitInSession();


        $this->assertEquals(array(1,3,7,1000),$helper->flexibleSearch()->getMappedEntityIdsForVehicle($civic2000));

    }

    function testShouldShowAllProductsForVehicleRangeWithUniversalMapping() {
        $civic2000 = $this->createVehicle(array('make'=>'Honda','model'=>'Civic','year'=>'2000'));
        $civic2001 = $this->createVehicle(array('make'=>'Honda','model'=>'Civic','year'=>'2001'));
        $civic2002 = $this->createVehicle(array('make'=>'Honda','model'=>'Civic','year'=>'2002'));
        $civic2003 = $this->createVehicle(array('make'=>'Honda','model'=>'Civic','year'=>'2003'));
        $this->insertMappingsForVehicle($civic2000,array(1,3,7));
        $this->insertMappingsForVehicle($civic2001,array(1,3,7,9));
        $this->insertMappingsForVehicle($civic2002,array(1,4,5,9));
        $this->insertMappingsForVehicle($civic2003,array(1,2,3,5));
        $this->insertUniversalFit(1000);

        $requestParams = array(
            'make' => $civic2000->getValue('make'),
            'model' => $civic2000->getValue('model'),
            'year_start' => $civic2000->getValue('year'),
            'year_end' => $civic2003->getValue('year')
        );
        $helper = $this->getHelper(array(),$requestParams);
        $helper->storeFitInSession();


        $this->assertEquals(array(1,2,3,4,5,7,9,1000),$helper->flexibleSearch()->doGetProductIds());

    }
}