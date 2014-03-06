<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_MappingTest extends VF_TestCase
{
    function testSave()
    {
        $vehicle = $this->createMMY();
        $mapping = new VF_Mapping(1, $vehicle, $this->getServiceContainer()->getSchemaClass(
        ), $this->getServiceContainer()->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass());
        $mapping_id = $mapping->save();
        $this->assertNotEquals(0, $mapping_id);
    }

    function testSaveRepeat()
    {
        $vehicle = $this->createMMY();
        $mapping = new VF_Mapping(1, $vehicle, $this->getServiceContainer()->getSchemaClass(
        ), $this->getServiceContainer()->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass());
        $mapping_id1 = $mapping->save();
        $mapping_id2 = $mapping->save();
        $this->assertEquals($mapping_id1, $mapping_id2, 'on repeated save should return existing mapping id');
    }

    function testAlreadyHasMapping()
    {
        $vehicle = $this->createMMY();
        $mapping = new VF_Mapping(1, $vehicle, $this->getServiceContainer()->getSchemaClass(
        ), $this->getServiceContainer()->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass());
        $mapping_id1 = $mapping->save();
        $mapping = new VF_Mapping(1, $vehicle, $this->getServiceContainer()->getSchemaClass(
        ), $this->getServiceContainer()->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass());
        $mapping_id2 = $mapping->save();
        $this->assertEquals($mapping_id1, $mapping_id2);
    }

    /**
     * @expectedException Exception
     */
    function testRequiresProduct()
    {
        $vehicle = $this->createMMY();
        $mapping = new VF_Mapping(0, $vehicle, $this->getServiceContainer()->getSchemaClass(
        ), $this->getServiceContainer()->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass());
        $mapping_id = $mapping->save();
    }

    function testShouldSaveMappingInSecondSchema()
    {
        $container = $this->createSchemaWithServiceContainer('foo,bar');
        $vehicle = $this->createVehicle(array('foo' => '123', 'bar' => '456'), $container);
        $mapping = new VF_Mapping(1, $vehicle, $container->getSchemaClass(
        ), $container->getReadAdapterClass(), $container->getConfigClass());
        $id = $mapping->save();
        $this->assertTrue($id > 0, 'should save mapping in second schema');
    }
}