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
        $this->switchSchema('make,model,year');
    }

    function testPrevLevelsIncludingMake()
    {
        $schema = VF_Singleton::getInstance()->schema();
        $this->assertEquals(array('make'), $schema->getPrevLevelsIncluding('make'));
    }

    function testPrevLevelsIncludingsModel()
    {
        $schema = VF_Singleton::getInstance()->schema();
        $this->assertEquals(array('make', 'model'), $schema->getPrevLevelsIncluding('model'));
    }

    function testPrevLevelssIncludingYear()
    {
        $schema = VF_Singleton::getInstance()->schema();
        $this->assertEquals(array('make', 'model', 'year'), $schema->getPrevLevelsIncluding('year'));
    }
}