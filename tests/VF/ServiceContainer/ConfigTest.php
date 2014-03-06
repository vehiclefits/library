<?php

/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 *
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @author     Kyle Cannon <kyle.d.cannon@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_ServiceContainer_ConfigTest extends VF_ServiceContainer_TestCase
{

    public function testDefaultConfigIsLoaded()
    {
        $configClass = $this->container1->getConfigClass();

        $this->assertEquals('loading', $configClass->search->loadingText);
    }

    public function testMerge() {
        $this->assertEquals('loading', $this->container1->getConfigClass()->search->loadingText);
        $config = new Zend_Config(array('search' => array('loadingText' => 'Please Wait')), true);
        $this->container1->getConfigClass()->merge($config);
        $this->assertEquals('Please Wait', $this->container1->getConfigClass()->search->loadingText);
    }

    public function testSetConfigEnsuresDefaultSectionsExist()
    {
        $this->markTestIncomplete();
    }

} 