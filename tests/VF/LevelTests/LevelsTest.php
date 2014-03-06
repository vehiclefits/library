<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_LevelTests_LevelsTest extends VF_TestCase
{

    protected function doSetUp()
    {
        parent::doSetUp();
    }

    function testGetType()
    {
        $entity = $this->vfLevel('make');
        $this->assertSame(self::ENTITY_TYPE_MAKE, $entity->getType());
    }

    function testgetNextLevelMake()
    {
        $entity = $this->vfLevel('make');
        $this->assertEquals(self::ENTITY_TYPE_MODEL, $entity->getNextLevel(), 'getNextLevel should return "model" for a entity of type "make"');
    }

    function testgetNextLevelModel()
    {
        $entity = $this->vfLevel('model');
        $this->assertEquals(self::ENTITY_TYPE_YEAR, $entity->getNextLevel(), 'getNextLevel should return "year" for a entity of type "model"');
    }

    function testgetNextLevelYear()
    {
        $entity = $this->vfLevel('year');
        $this->assertEquals('', $entity->getNextLevel(), 'getNextLevel should return emtpy string if called on a leaf level');
    }

    function testgetPrevLevelMake()
    {
        $entity = $this->vfLevel('make');
        $this->assertEquals('', $entity->getPrevLevel(), 'getPrevLevel should return emtpy string if called on a root level');
    }

    function testgetPrevLevelModel()
    {
        $entity = $this->vfLevel('model');
        $this->assertEquals(self::ENTITY_TYPE_MAKE, $entity->getPrevLevel(), 'getPrevLevel should return "make" for a entity of type "model"');
    }

    function testgetPrevLevelYear()
    {
        $entity = $this->vfLevel('year');
        $this->assertEquals(self::ENTITY_TYPE_MODEL, $entity->getPrevLevel(), 'getPrevLevel should return "model" for a entity of type "year"');
    }
}
