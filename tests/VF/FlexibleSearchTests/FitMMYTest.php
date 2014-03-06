<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 *
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_FlexibleSearchTests_FitMMYTest extends VF_TestCase
{

//    function testGetFitId()
//    {
//        $vehicle = $this->createMMY();
//        $helper = $this->getHelper(array(), array(
//            'make' => $vehicle->getLevel('make')->getId(),
//            'model' => $vehicle->getLevel('model')->getId(),
//            'year' => $vehicle->getLevel('year')->getId()
//        ));
//        $this->assertEquals($vehicle->getLevel('year')->getId(), $helper->vehicleSelection()->getLeafValue());
//    }

    function testShouldGetFlexibleDefinition()
    {
        $vehicle1 = $this->createVehicle(array('make' => 'Honda1', 'model' => 'Civic', 'year' => '2000'));
        $vehicle2 = $this->createVehicle(array('make' => 'Honda2', 'model' => 'Civic', 'year' => '2000'));
        $_SESSION = $vehicle2->toValueArray();
        $flexibleSearch = $this->getServiceContainer()->getFlexibleSearchClass();
        $this->assertEquals($vehicle2->toValueArray(), $flexibleSearch->getFlexibleDefinition()->toValueArray());
    }

    function testGetFitId2()
    {
        $vehicle = $this->createMMY();
        $helper = $this->getHelper(
            array(),
            array(
                 'make'  => $vehicle->getLevel('make')->getId(),
                 'model' => $vehicle->getLevel('model')->getId(),
                 'year'  => $vehicle->getLevel('year')->getId()
            )
        );
        $selectedVehicles = $helper->vehicleSelection();
        $this->assertEquals($vehicle->getLevel('year')->getId(), $selectedVehicles[0]->getLeafValue());
    }

    function testGetFitIdMultiTree()
    {
        $vehicle = $this->createMMY();
        $helper = $this->getHelper(array(), array('fit' => $vehicle->getLevel('year')->getId()));
        $this->assertEquals($vehicle->getLevel('year')->getId(), $helper->vehicleSelection()->getLeafValue());
    }

    function testClearsWhenPassed0ForEachValue()
    {
        $_SESSION = array('make' => 1, 'model' => 1, 'year' => 1);
        $helper = $this->getHelper(array(), array('make' => 0, 'model' => 0, 'year' => 0));

        $this->assertEquals(
            0,
            count($helper->vehicleSelection()),
            'request values should take precedence over session value'
        );
        $this->assertFalse(isset($_SESSION['make']), 'passing 0 in request should reset session value');
    }

    function testShouldStoreInSession()
    {
        $_SESSION = array('make' => null, 'model' => null, 'year' => null);
        $vehicle = $this->createMMY();
        $helper = $this->getHelper(array(), $vehicle->toValueArray());
        $helper->storeFitInSession();
        unset($_SESSION['garage']);
        $this->assertEquals($vehicle->toValueArray(), $_SESSION, 'should store vehicle in session');
    }

    function testShouldNotStoreInSession()
    {
        $_SESSION = array('make' => null, 'model' => null, 'year' => null);
        $vehicle = $this->createMMY();
        $helper = $this->getHelper(array('search' => array('storeVehicleInSession' => '')), $vehicle->toValueArray());
        $helper->storeFitInSession();
        unset($_SESSION['garage']);
        $this->assertNotEquals($vehicle->toValueArray(), $_SESSION, 'should not store in session when disabled');
    }

    function testShouldNotStoreInSessionWhenFalse()
    {
        $_SESSION = array('make' => null, 'model' => null, 'year' => null);
        $vehicle = $this->createMMY();
        $helper = $this->getHelper(
            array('search' => array('storeVehicleInSession' => 'false')),
            $vehicle->toValueArray()
        );
        $helper->storeFitInSession();
        unset($_SESSION['garage']);
        $this->assertNotEquals($vehicle->toValueArray(), $_SESSION, 'should not store in session when disabled');
    }

    function testShouldNotStoreInSession2()
    {
        $_SESSION = array('make' => null, 'model' => null, 'year' => null);
        $config = new Zend_Config(array('search' => array('storeVehicleInSession' => '')),true);

        $vehicle = $this->createMMY();
        $this->getServiceContainer()->getConfigClass()->merge($config);
        $this->getServiceContainer()->getRequestClass()->setParams($vehicle->toValueArray());
        $this->getServiceContainer()->getFlexibleSearchClass()->storeFitmentInSession();

        unset($_SESSION['garage']);
        $this->assertNotEquals($vehicle->toValueArray(), $_SESSION, 'should get global configuration');
    }

    function testShouldNotHaveRequest()
    {
        $helper = $this->getHelper(array(), array());
        $this->assertFalse($helper->flexibleSearch()->hasRequest());
    }

    function testShouldNotHaveGETRequest()
    {
        $helper = $this->getHelper(array(), array());
        $this->assertFalse($helper->flexibleSearch()->hasGETRequest());
    }

    function testShouldNotHaveSESSIONRequest()
    {
        $helper = $this->getHelper(array(), array());
        $this->assertFalse($helper->flexibleSearch()->hasSESSIONRequest());
    }

    function testShouldHaveSESSIONRequest()
    {
        $vehicle = $this->createMMY();
        $_SESSION = $vehicle->toValueArray();
        $helper = $this->getHelper(array(), array());
        $this->assertTrue($helper->flexibleSearch()->hasSESSIONRequest());
    }

    function testGetsMakeIdFromSession()
    {
        $vehicle = $this->createMMY();
        $_SESSION = $vehicle->toValueArray();
        $helper = $this->getHelper(array(), array());
        $this->assertEquals(
            $vehicle->getLevel('make')->getId(),
            $helper->flexibleSearch()->getValueForSelectedLevel('make')
        );
    }

    function testGetsYearIdFromSession()
    {
        $vehicle = $this->createMMY();
        $_SESSION = $vehicle->toValueArray();
        $helper = $this->getHelper(array(), array());
        $this->assertEquals(
            $vehicle->getLevel('year')->getId(),
            $helper->flexibleSearch()->getValueForSelectedLevel('year')
        );
    }

    function testGetsIdFromSession()
    {
        $vehicle = $this->createMMY();
        $_SESSION = $vehicle->toValueArray();
        $helper = $this->getHelper(array(), array());
        $selectedVehicles = $helper->vehicleSelection();
        $this->assertEquals(
            $vehicle->getLevel('year')->getId(),
            $selectedVehicles[0]->getLeafValue(),
            'gets fit id from session if there is no request'
        );
        $this->assertEquals(
            $vehicle->getLevel('year')->getId(),
            $helper->storeFitInSession(),
            'storeFitmentInSession() should return leafID'
        );
    }

    function testShouldAutomaticallyClearInvalidSession()
    {
        $_SESSION = array('make' => 99, 'model' => 99, 'year' => 99);
        $helper = $this->getHelper();
        $flexibleSearch = $this->getServiceContainer()->getFlexibleSearchClass();
        $this->assertFalse(
            $flexibleSearch->getFlexibleDefinition(),
            'when fitment is deleted should automatically clear invalid session'
        );
    }

    function testGetValueForSelectedLevel()
    {
        $vehicle = $this->createMMY();
        $helper = $this->getHelper(array(), $vehicle->toValueArray());
        $this->assertEquals($vehicle->getLevel('make')->getId(), $helper->getValueForSelectedLevel('make'));
    }

    function testGetLevelAndValueForSelectedPreviousLevels()
    {
        $vehicle1 = $this->createVehicle(
            array('make' => 'Toyota', 'model' => 'Camry', 'year' => '2002', 'engine' => '2.4L 2AZ-FE')
        );
        $vehicle2 = $this->createVehicle(
            array('make' => 'Chevrolet', 'model' => 'Astro Van', 'year' => '2001', 'engine' => '4.3L V6')
        );
        $vehicle3 = $this->createVehicle(
            array('make' => 'Chevrolet', 'model' => 'Tahoe', 'year' => '2002', 'engine' => '5.3L V8')
        );

        $this->setServiceContainerWithRequest($this->getRequest($vehicle3->toValueArray()));
        $flexibleSearch = $this->getServiceContainer()->getFlexibleSearchClass();
        $expectedResponse = array(
            'make'  => $vehicle3->getLevel('make')->getId(),
            'model' => $vehicle3->getLevel('model')->getId(),
            'year'  => $vehicle3->getLevel('year')->getId()
        );
        $this->assertEquals($expectedResponse, $flexibleSearch->getLevelAndValueForSelectedPreviousLevels('engine'));
    }
}