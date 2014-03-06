<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_LevelsTests_ConformTests_MMYTest extends VF_TestCase
{
    protected function doSetUp()
    {
        parent::doSetUp();
    }

    function testConformsLevelMake()
    {
        $honda = $this->vfLevel('make');
        $honda->setTitle('Honda');
        $honda->save();
        $honda2 = $this->vfLevel('make');
        $honda2->setTitle('Honda');
        $honda2->save();
        $this->assertEquals($honda->getId(), $honda2->getId(), 'when saving two makes with same title, they should get the same id');
    }

    function testConformsLevelModel()
    {
        $honda = $this->vfLevel('make');
        $honda->setTitle('Honda');
        $honda_make_id = $honda->save();
        $civic = $this->vfLevel('model');
        $civic->setTitle('Civic');
        $civic->save($honda_make_id);
        $civic2 = $this->vfLevel('model');
        $civic2->setTitle('Civic');
        $civic2->save($honda_make_id);
        $this->assertEquals($civic->getId(), $civic2->getId(), 'when saving two models with the same titles & under the same make, they should get the same id');
    }

    function testDoesntConformModelFromDiffrentMake()
    {
        $honda = $this->vfLevel('make');
        $honda->setTitle('Honda');
        $honda_make_id = $honda->save();
        $civic = $this->vfLevel('model');
        $civic->setTitle('Civic');
        $civic->save($honda_make_id);
        $ford = $this->vfLevel('make');
        $ford->setTitle('Ford');
        $ford_make_id = $ford->save();
        $civic2 = $this->vfLevel('model');
        $civic2->setTitle('Civic');
        $civic2->save($ford_make_id);
        $this->assertEquals($civic->getId(), $civic2->getId(), 'when saving two models with same title, but under different makes, they should get same ids');
    }
}
