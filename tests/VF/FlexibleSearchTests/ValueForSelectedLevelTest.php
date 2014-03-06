<?php

/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 *
 * @author     Kyle Cannon <kyle.d.cannon@gmail.com>
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_FlexibleSearchTests_ValueForSelectedLevelTest extends VF_TestCase
{
    /**
     * @covers VF_FlexibleSearch::doesLevelHaveDefaultLoadingTextAsValue
     */
    function testWhenLoadingTextIsPassed_ReturnsTrue()
    {
        $this->setRequestParams(array('make' => 'Honda', 'model' => 'loading'));
        $this->assertFalse($this->getServiceContainer()->getFlexibleSearchClass()->doesLevelHaveDefaultLoadingTextAsValue('make'));
        $this->assertTrue($this->getServiceContainer()->getFlexibleSearchClass()->doesLevelHaveDefaultLoadingTextAsValue('model'));
    }

    /**
     * @covers VF_FlexibleSearch::doesLevelHaveDefaultLoadingTextAsValue
     */
    function testWhenCustomLoadingTextIsPassed_ReturnsTrue()
    {
        $this->getServiceContainer()->getConfigClass()->merge(
            new Zend_Config(array('search' => array('loadingText' => 'Loading Please Wait')), true)
        );
        $this->setRequestParams(array('make' => 'Honda', 'model' => 'Loading Please Wait'));
        $this->assertFalse($this->getServiceContainer()->getFlexibleSearchClass()->doesLevelHaveDefaultLoadingTextAsValue('make'));
        $this->assertTrue($this->getServiceContainer()->getFlexibleSearchClass()->doesLevelHaveDefaultLoadingTextAsValue('model'));
    }

} 