<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_SchemaTests_MMY_SchemaTest extends VF_TestCase
{
    function doSetUp()
    {
        $this->switchSchema('make,model,year');
    }

    function testLevels()
    {
        $schema = VF_Singleton::getInstance()->schema();
        $this->assertEquals(array('make', 'model', 'year'), $schema->getLevels(), 'should get levels');
    }

    function testGetRootLevel()
    {
        $schema = VF_Singleton::getInstance()->schema();
        $this->assertEquals(self::ENTITY_TYPE_MAKE, $schema->getRootLevel(), 'root level should be "make"');
    }

    function testGetLeafLevel()
    {
        $schema = VF_Singleton::getInstance()->schema();
        $this->assertEquals(self::ENTITY_TYPE_YEAR, $schema->getLeafLevel(), 'root level should be "year"');
    }

    function testPrevLevelMake()
    {
        $schema = VF_Singleton::getInstance()->schema();
        $this->assertEquals(false, $schema->getPrevLevel('make'));
    }

    function testPrevLevelModel()
    {
        $schema = VF_Singleton::getInstance()->schema();
        $this->assertEquals('make', $schema->getPrevLevel('model'));
    }

    function testNextLevelMake()
    {
        $schema = VF_Singleton::getInstance()->schema();
        $this->assertEquals('model', $schema->getNextLevel('make'));
    }

    function testNextLevelModel()
    {
        $schema = VF_Singleton::getInstance()->schema();
        $this->assertEquals('year', $schema->getNextLevel('model'));
    }

    function testNextLevelYear()
    {
        $schema = VF_Singleton::getInstance()->schema();
        $this->assertFalse($schema->getNextLevel('year'));
    }

    function testLevelIsBefore()
    {
        $schema = VF_Singleton::getInstance()->schema();
        $this->assertTrue($schema->levelIsBefore('make', 'model'));
    }

    function testLevelIsBefore2()
    {
        $schema = VF_Singleton::getInstance()->schema();
        $this->assertFalse($schema->levelIsBefore('model', 'make'));
    }

    function testGetLevelsExceptLeaf()
    {
        $schema = VF_Singleton::getInstance()->schema();
        $this->assertSame(array('make', 'model'), $schema->getLevelsExceptLeaf());
    }

    function testGetLevelsExcluding()
    {
        $schema = VF_Singleton::getInstance()->schema();
        $this->assertSame(array('make', 'year'), $schema->getLevelsExcluding('model'));
    }

    function testGetLevelsExceptRoot()
    {
        $schema = VF_Singleton::getInstance()->schema();
        $this->assertSame(array('model', 'year'), $schema->getLevelsExceptRoot());
    }
}
