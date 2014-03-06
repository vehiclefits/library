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
class VF_SchemaTests_MultipleTest extends VF_TestCase
{
    function doSetUp()
    {
        $this->switchSchema('make,model,option,year');
    }

    function testCreateNewSchemaId()
    {
        $schema = $this->createSchemaWithServiceContainer('foo,bar')->getSchemaClass();
        $this->assertTrue($schema->id() > 0, 'should assign new ID when create new schema');
    }

    function testShouldAssumeDefaultSchemaWhenNotSpecified()
    {
        $container = $this->createSchemaWithServiceContainer('foo,bar');
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertEquals(array('make', 'model', 'option', 'year'), $schema->getLevels(), 'should assume default schema when not specified');
    }

    function testGetNewSchemasLevelsOfThreeSchemas()
    {
        $schema1 = new VF_Schema($this->getServiceContainer()->getSchemaClass()->id(), $this->getReadAdapter(), $this->getServiceContainer()->getConfigClass());
        $container2 = $this->createSchemaWithServiceContainer('foo,bar');
        $schema2 = new VF_Schema($container2->getSchemaClass()->id(), $this->getReadAdapter(), $this->getServiceContainer()->getConfigClass());
        $container3 = $this->createSchemaWithServiceContainer('foo2,bar2');
        $schema3 = new VF_Schema($container3->getSchemaClass()->id(), $this->getReadAdapter(), $this->getServiceContainer()->getConfigClass());

        $this->assertEquals(array('make','model','option','year'), $schema1->getLevels(), 'should get the correct levels when we specify which schema');
        $this->assertEquals(array('foo', 'bar'), $schema2->getLevels(), 'should get the correct levels when we specify which schema');
        $this->assertEquals(array('foo2', 'bar2'), $schema3->getLevels(), 'should get the correct levels when we specify which schema');
    }

    function testShouldGetSchemaById()
    {
        $schema = $this->createSchemaWithServiceContainer('foo,bar')->getSchemaClass();
        $schemaID = $schema->id();
        $new_schema = new VF_Schema($schemaID, $this->getReadAdapter(), $this->getServiceContainer()->getConfigClass());
        $this->assertEquals(array('foo', 'bar'), $new_schema->getLevels(), 'should look up schema specified by ID passed to constructor');
    }
}