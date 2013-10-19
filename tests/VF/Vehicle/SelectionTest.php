<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Vehicle_SelectionTest extends VF_TestCase
{
    function testShouldNotContainVehicle()
    {
        $selection = new VF_Vehicle_Selection(array(
            'make' => 'Honda',
            'model' => 'Civic',
        ));
        $this->assertFalse($selection->contains(array(
            'make' => 'Ford'
        )), 'should not contain vehicle where level differs from selection');
    }

    function testShouldContainVehicle()
    {
        $selection = new VF_Vehicle_Selection(array(
            'make' => 'Honda',
            'model' => 'Civic',
        ));
        $this->assertTrue($selection->contains(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2000',
        )), 'should contain vehicle more specific than the selection');
    }

    function testShouldDetectEarliestYearInRange()
    {
        $selection = new VF_Vehicle_Selection(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year_start' => '2000',
            'year_end' => '2005',
        ));
        $this->assertEquals('2000', $selection->earliestYear(), 'should detect earliest year in year range');
    }

    function testShouldDetectLatestYearInRange()
    {
        $selection = new VF_Vehicle_Selection(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year_start' => '2000',
            'year_end' => '2005',
        ));
        $this->assertEquals('2005', $selection->latestYear(), 'should detect latest year in year range');
    }

    function testShouldContainVehicleWithinRange()
    {
        $selection = new VF_Vehicle_Selection(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year_start' => '2000',
            'year_end' => '2005',
        ));
        $this->assertTrue($selection->contains(array(
            'make' => 'Honda',
            'model' => 'Civic',
            'year' => '2001',
        )), 'should contain a vehicle within the year range');
    }

}