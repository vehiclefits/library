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
class VF_Vehicle_FinderTests_FindAllTest extends VF_Vehicle_FinderTests_TestCase
{
    protected function doSetUp()
    {
        $this->switchSchema('make,model,year');
    }

    function testFindOneVehicle()
    {
        $expectedVehicle = $this->createVehicle(array(
            'make'=>'Honda',
            'model'=>'Civic',
            'year'=>'2000'
        ));
        $vehicles = $this->getFinder()->findAll();
        $this->assertEquals($expectedVehicle->__toString(), $vehicles[0]->__toString(), 'should list the one vehicle');
    }

    function testShouldFindMultipleVehicles()
    {
        $this->createVehicles(5);
        $vehicles = $this->getFinder()->findAll();
        $this->assertEquals(5, count($vehicles), 'should find 5 vehicles');
    }

    function createVehicles($count)
    {
        for($i=1; $i<=$count; $i++) {
            $this->createVehicle(array(
                'make'=>'Honda',
                'model'=>'Civic',
                'year'=>uniqid()
            ));
        }
    }
}