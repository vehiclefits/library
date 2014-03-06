<?php

/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 *
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @author     Kyle Cannon <kyle.d.cannon@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_ConfigTests_SettingsTest extends VF_TestCase
{

    function testGetDefaultSearchOptionText()
    {
        $helper = $this->getHelper(array('search' => array('defaultText' => 'foo')));
        $this->assertEquals('foo', $helper->getConfigClass()->getDefaultSearchOptionText());
    }

    function testGetDefaultSearchOptionTextPerLevel()
    {
        $helper = $this->getHelper(array('search' => array('defaultText' => '- Pick %s -')));
        $this->assertEquals('- Pick Make -', $helper->getConfigClass()->getDefaultSearchOptionText('make'));
    }

    function testGetDefaultSearchOptionTextDefault()
    {
        $helper = $this->getHelper(array('search' => array()));
        $this->assertEquals('-please select-', $helper->getConfigClass()->getDefaultSearchOptionText());
    }

    function testLabelsDefaultsTrue()
    {
        $helper = $this->getHelper(array('search' => array()));
        $this->assertTrue($helper->getConfigClass()->showLabels());
    }

    function testLabelsShouldDisable()
    {
        $helper = $this->getHelper(array('search' => array('labels' => false)));
        $this->assertFalse($helper->getConfigClass()->showLabels());
    }

    function testLabelsShouldEndable()
    {
        $helper = $this->getHelper(array('search' => array('labels' => true)));
        $this->assertTrue($helper->getConfigClass()->showLabels());
    }

    function testDefaultBrTag()
    {
        $helper = $this->getHelper(array('search' => array()));
        $this->assertTrue($helper->getConfigClass()->displayBrTag());
    }

    function testDefaultDirectory()
    {
        $helper = $this->getHelper(array('directory' => array('enable' => true)));
        $this->assertTrue($helper->getConfigClass()->enableDirectory());
    }

    function testBrTag1()
    {
        $helper = $this->getHelper(array('search' => array('insertBrTag' => true)));
        $this->assertTrue($helper->getConfigClass()->displayBrTag());
    }

    function testBrTag2()
    {
        $helper = $this->getHelper(array('search' => array('insertBrTag' => false)));
        $this->assertFalse($helper->getConfigClass()->displayBrTag());
    }

    function testLoadingTextDefault()
    {
        $helper = $this->getHelper(array('search' => array()));
        $this->assertEquals('loading', $helper->getConfigClass()->getLoadingText());
    }

    function testLoadingText()
    {
        $helper = $this->getHelper(array('search' => array('loadingText' => 'test')));
        $this->assertEquals('test', $helper->getConfigClass()->getLoadingText());
    }

    function testLoadingTextBlank()
    {
        $helper = $this->getHelper(array('search' => array('loadingText' => '')));
        $this->assertEquals('', $helper->getConfigClass()->getLoadingText());
    }

    function testGetDefaultLoadingTextNotDefaultOrBlank() {
        $helper = $this->getHelper(array('search' => array('loadingText' => 'test')));
        $this->assertEquals('test', $helper->getConfigClass()->getLoadingText());
    }

} 