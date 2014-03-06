<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Level_FinderTests_ListInUseMultipleSchemaTest extends VF_TestCase
{

    function testShouldListFromSecondSchema()
    {
        $container = $this->createSchemaWithServiceContainer('foo,bar');
        $vehicle = $this->createVehicle(array('foo' => '123', 'bar' => '456'), $container);
        $mapping = new VF_Mapping(1, $vehicle, $container->getSchemaClass(), $container->getReadAdapterClass(
        ), $container->getConfigClass());
        $mapping->save();
        $foo = $this->vfLevel('foo', null, $container);
        $actual = $foo->listInUse();
        $this->assertEquals('123', $actual[0], 'should list for level in 2nd schema "foo"');
    }
}