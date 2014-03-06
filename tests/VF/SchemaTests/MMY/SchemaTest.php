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
        parent::doSetUp();
    }

    function testLevels()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertEquals(array('make', 'model', 'year'), $schema->getLevels(), 'should get levels');
    }

    function testGetRootLevel()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertEquals(self::ENTITY_TYPE_MAKE, $schema->getRootLevel(), 'root level should be "make"');
    }

    function testGetLeafLevel()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertEquals(self::ENTITY_TYPE_YEAR, $schema->getLeafLevel(), 'root level should be "year"');
    }

    function testPrevLevelMake()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertEquals(false, $schema->getPrevLevel('make'));
    }

    function testPrevLevelModel()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertEquals('make', $schema->getPrevLevel('model'));
    }

    function testNextLevelMake()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertEquals('model', $schema->getNextLevel('make'));
    }

    function testNextLevelModel()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertEquals('year', $schema->getNextLevel('model'));
    }

    function testNextLevelYear()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertFalse($schema->getNextLevel('year'));
    }

    function testLevelIsBefore()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertTrue($schema->levelIsBefore('make', 'model'));
    }

    function testLevelIsBefore2()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertFalse($schema->levelIsBefore('model', 'make'));
    }

    function testGetLevelsExceptLeaf()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertSame(array('make', 'model'), $schema->getLevelsExceptLeaf());
    }

    function testGetLevelsExcluding()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertSame(array('make', 'year'), $schema->getLevelsExcluding('model'));
    }

    function testGetLevelsExceptRoot()
    {
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertSame(array('model', 'year'), $schema->getLevelsExceptRoot());
    }
}
