<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_SchemaTests_MMY_PrevLevelsTest extends VF_TestCase
{
    function doSetUp()
    {
        parent::doSetUp();
    }

    function testPrevLevelsMake()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertEquals(array(), $schema->getPrevLevels('make'));
    }

    function testPrevLevelsModel()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertEquals(array('make'), $schema->getPrevLevels('model'));
    }

    function testPrevLevelsYear()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertEquals(array('make', 'model'), $schema->getPrevLevels('year'));
    }
}