<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class VF_Vehicle_FinderTests_DistinctTest extends VF_Vehicle_FinderTests_TestCase
{

    function testShouldFindDistinct()
    {
        return $this->markTestIncomplete('placeholder test for selecting distinct vehicles');

        $this->createVehicle(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2000'
        ));
        $this->createVehicle(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2001'
        ));
        $vehicles = $this->getFinder()->findDistinctByLevels(array(
            'make' => 'Honda',
            'model' => 'Civic',
        ));
        $this->assertEquals(1, count($vehicles), 'should find by levels');

        $firstVehicle = $vehicles[0];
        $this->assertEquals('Honda Civic', $firstVehicle->__toString());
    }
}