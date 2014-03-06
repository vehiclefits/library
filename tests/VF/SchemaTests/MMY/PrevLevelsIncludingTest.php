<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_SchemaTests_MMY_PrevLevelsIncludingTest extends VF_TestCase
{
    function doSetUp()
    {
        parent::doSetUp();
    }

    function testPrevLevelsIncludingMake()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertEquals(array('make'), $schema->getPrevLevelsIncluding('make'));
    }

    function testPrevLevelsIncludingsModel()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertEquals(array('make', 'model'), $schema->getPrevLevelsIncluding('model'));
    }

    function testPrevLevelssIncludingYear()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertEquals(array('make', 'model', 'year'), $schema->getPrevLevelsIncluding('year'));
    }
}