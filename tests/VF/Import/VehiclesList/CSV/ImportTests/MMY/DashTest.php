<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Import_VehiclesList_CSV_ImportTests_MMY_DashTest extends VF_Import_TestCase
{

    protected $csvData;
    protected $csvFile;

    function doSetUp()
    {
        parent::doSetUp();
    }

    function test1()
    {
        $this->importVehiclesList('make, model, year
honda, ci-vic, 2000
honda, ci-vic, 2001');
        $finder = $this->vehicleFinder();
        $vehicles = $finder->findByLevels(array('make' => 'honda', 'model' => 'ci-vic', 'year' => '2000'));
        $this->assertEquals(1, count($vehicles));
    }

    function test2()
    {
        $this->importVehiclesList('make, model, year
honda, ci-vic, 2000
honda, ci-vic, 2001');
        $finder = $this->vehicleFinder();
        $vehicles = $finder->findByLevels(array('make' => 'honda', 'model' => 'ci-vic', 'year' => '2001'));
        $this->assertEquals(1, count($vehicles));
    }

    function test3()
    {
        $this->importVehiclesList('make, model, year
honda, ci-vic, 2000
honda, ci-vic, 2001');
        $finder = $this->vehicleFinder();
        $result = $this->query('select count(*) from elite_1_definition;');
        $this->assertEquals(2, $result->fetchColumn());
    }

    function test4()
    {
        $this->importVehiclesList('make, model, year
honda, ci-vic, 2000
honda, civi-c, 2000');
        $finder = $this->vehicleFinder();
        $result = $this->query('select count(*) from elite_1_definition;');
        $this->assertEquals(2, $result->fetchColumn());
    }
}