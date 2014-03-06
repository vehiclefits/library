<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_VehicleMultipleSchemaTest extends VF_TestCase
{

    function testVehicleShouldHaveRightSchema()
    {
        $container = $this->createSchemaWithServiceContainer('foo,bar');
        $schema = $container->getSchemaClass();
        $adapter = $container->getReadAdapterClass();
        $config = $container->getConfigClass();
        $levelFinder = $container->getLevelFinderClass();
        $vehicleFinder = $container->getVehicleFinderClass();
        $vehicle = VF_Vehicle::create(
            $schema,
            $adapter,
            $config,
            $levelFinder,
            $vehicleFinder,
            array('foo' => 'valfoo', 'bar' => 'valbar')
        );
        $this->assertEquals($schema->id(), $vehicle->schema()->id(), 'vehicle should have right schema');
    }

    function testLevelsShouldHaveRightSchema()
    {
        $container = $this->createSchemaWithServiceContainer('foo,bar');
        $schema = $container->getSchemaClass();
        $adapter = $container->getReadAdapterClass();
        $config = $container->getConfigClass();
        $levelFinder = $container->getLevelFinderClass();
        $vehicleFinder = $container->getVehicleFinderClass();
        $vehicle = VF_Vehicle::create(
            $schema,
            $adapter,
            $config,
            $levelFinder,
            $vehicleFinder, array('foo' => 'valfoo', 'bar' => 'valbar'));
        $this->assertEquals($schema->id(), $vehicle->getLevel('foo')->getSchema()->id(), 'levels should have right schema');
    }

    function testShouldSaveLevel()
    {
        $container = $this->createSchemaWithServiceContainer('foo,bar');
        $schema = $container->getSchemaClass();
        $adapter = $container->getReadAdapterClass();
        $config = $container->getConfigClass();
        $levelFinder = $container->getLevelFinderClass();
        $vehicleFinder = $container->getVehicleFinderClass();
        $vehicle = VF_Vehicle::create(
            $schema,
            $adapter,
            $config,
            $levelFinder,
            $vehicleFinder, array('foo' => 'valfoo', 'bar' => 'valbar'));
        $vehicle->save();
        $levelFinder = $container->getLevelFinderClass();
        $foundLevel = $levelFinder->find('foo', $vehicle->getValue('foo'));
        $this->assertEquals($vehicle->getValue('foo'), $foundLevel->getId(), 'should save & find level');
    }

    function testSaveParenetheses()
    {
        $container = $this->createSchemaWithServiceContainer('foo,bar');
        $schema = $container->getSchemaClass();
        $adapter = $container->getReadAdapterClass();
        $config = $container->getConfigClass();
        $levelFinder = $container->getLevelFinderClass();
        $vehicleFinder = $container->getVehicleFinderClass();
        $vehicle = VF_Vehicle::create(
            $schema,
            $adapter,
            $config,
            $levelFinder,
            $vehicleFinder, array('foo' => 'valfoo', 'bar' => 'valbar'));
        $vehicle->save();
        $vehicleExists = $this->vehicleExists(array('foo' => 'valfoo', 'bar' => 'valbar'), false, $container);
        $this->assertTrue($vehicleExists, 'should find vehicles in different schema');
    }
}