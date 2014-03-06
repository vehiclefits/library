<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Schema_GeneratorTest extends VF_TestCase
{
    function doSetUp()
    {
        $this->schemaGenerator()->dropExistingTables();
    }

    function tearDown()
    {
        $this->schemaGenerator()->dropExistingTables();
    }

    /**
     * @expectedException VF_Level_Exception
     */
    function testShouldThrowExceptionForLessThanTwoLevels()
    {
        $this->schemaGenerator()->execute(array('make'));
    }

    function testShouldDefaultToMasterSchema()
    {
        $this->schemaGenerator()->execute(array('make', 'model', 'year'));
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertEquals(1, $schema->id(), 'schema should default to master schema represented by ID=1');
    }

    function testMasterSchemaShouldCreateTablesWithID1()
    {
        $this->schemaGenerator()->execute(array('make', 'model', 'year'));
        $expectedTable = 'elite_level_1_make';
        $tables = $this->getReadAdapter()->listTables();
        $this->assertTrue(in_array($expectedTable, $tables), "master schema should create make with '1' in it's name");
    }

    function testMMY()
    {
        $this->schemaGenerator()->execute(array('make', 'model', 'year'));
        $this->assertEquals(array('make', 'model', 'year'), $this->getServiceContainer()->getSchemaClass()->getLevels(), 'should switch levels to MMY');
    }

    function testYMM()
    {
        $this->schemaGenerator()->execute(array('year', 'make', 'model'));
        $this->assertEquals(array('year', 'make', 'model'), $this->getServiceContainer()->getSchemaClass()->getLevels(), 'should switch levels to YMM');
    }

    function testYMM_MakeShouldHaveParent_WhenNotGlobal()
    {
        $this->schemaGenerator()->execute(array('year', 'make', 'model'));
        $this->assertTrue($this->getServiceContainer()->getSchemaClass()->hasParent('make'), 'make should have parent when not global');
    }

    function testShouldGenerateMultipleSchemas()
    {
        $this->schemaGenerator()->execute(array('make', 'model', 'year'));
        $schema = $this->createSchemaWithServiceContainer('foo,bar')->getSchemaClass();
        $expectedTable = 'elite_level_' . $schema->id() . '_foo';
        $tables = $this->getReadAdapter()->listTables();
        $this->assertTrue(in_array($expectedTable, $tables), 'should create table for new schema `elite_level_x_foo`');
    }

}