<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_AjaxTests_MultipleSchemaTest extends VF_AjaxTests_AjaxTestCase
{

    function testShouldListRootLevel_WhenCalledFromFrontend()
    {
        $container = $this->createSchemaWithServiceContainer('foo,bar');
        $schema = $container->getSchemaClass();
        $schema_id = $schema->id();
        $this->setServiceContainer(new VF_ServiceContainer($schema_id, $this->getRequest(), $this->getReadAdapter(),new Zend_Config(array())));

        $vehicle = $this->createVehicle(array('foo' => '123', 'bar' => '456'), $container);
        $mapping = new VF_Mapping(1, $vehicle, $this->getServiceContainer()->getSchemaClass(
        ), $this->getServiceContainer()->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass());
        $mapping->save();
        ob_start();
        $_GET['front'] = 1;
        $_GET['requestLevel'] = 'foo';
        $this->getAjax()->execute();
        $actual = ob_get_clean();
        $this->assertEquals('<option value="123">123</option>', $actual, 'should list root levels from 2nd schema');
    }

    function testShouldListChildLevel_WhenCalledFromFrontend()
    {
        $container = $this->createSchemaWithServiceContainer('foo,bar');
        $schema = $container->getSchemaClass();
        $schema_id = $schema->id();
        $this->setServiceContainer(new VF_ServiceContainer($schema_id, $this->getRequest(), $this->getReadAdapter(),new Zend_Config(array())));

        $vehicle = $this->createVehicle(array('foo' => '123', 'bar' => '456'), $container);
        $mapping = new VF_Mapping(1, $vehicle, $this->getServiceContainer()->getSchemaClass(
        ), $this->getServiceContainer()->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass());
        $mapping->save();
        ob_start();
        $_GET['front'] = 1;
        $_GET['requestLevel'] = 'bar';
        $_GET['foo'] = '123';
        $this->getAjax()->execute();
        $actual = ob_get_clean();
        $this->assertEquals('<option value="456">456</option>', $actual, 'should list child levels from 2nd schema');
    }

    function testShouldListChildLevel_WhenCalledFromBackend()
    {
        $container = $this->createSchemaWithServiceContainer('foo,bar');
        $schema = $container->getSchemaClass();
        $schema_id = $schema->id();
        $this->setServiceContainer(new VF_ServiceContainer($schema_id, $this->getRequest(), $this->getReadAdapter(),new Zend_Config(array())));

        $vehicle = $this->createVehicle(array('foo' => '123', 'bar' => '456'), $container);
        $mapping = new VF_Mapping(1, $vehicle, $this->getServiceContainer()->getSchemaClass(
        ), $this->getServiceContainer()->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass());
        $mapping->save();
        ob_start();
        $_GET['requestLevel'] = 'bar';
        $_GET['foo'] = $vehicle->getValue('bar');
        $this->getAjax()->execute();
        $actual = ob_get_clean();
        $this->assertEquals('<option value="' . $vehicle->getValue('bar') . '">456</option>', $actual, 'should list child levels from 2nd schema');
    }
}