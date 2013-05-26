<?php
/**
 * Vehicle Fits
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@vehiclefits.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Vehicle Fits to newer
 * versions in the future. If you wish to customize Vehicle Fits for your
 * needs please refer to http://www.vehiclefits.com for more information.
 * @copyright  Copyright (c) 2013 Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_SingletonTest_URLsTest extends VF_TestCase
{
    /**
     * Should throw exception if trying to get process URL before one is set
     * @expectedException Exception
     */
    function testShouldThrowExceptionIfTryToGetProcessURLBeforeOneIsSet()
    {
        $singleton = new VF_Singleton();
        $singleton->processUrl();
    }

    function testShouldSetProcessURL()
    {
        $singleton = new VF_Singleton();
        $singleton->setProcessURL('foo');
        $this->assertEquals('foo',$singleton->processUrl(), 'should set process URL');
    }

    /**
     * Should throw exception if trying to get base URL before one is set
     * @expectedException Exception
     */
    function testShouldThrowExceptionIfTryToGetBaseURLBeforeOneIsSet()
    {
        $singleton = new VF_Singleton();
        $singleton->getBaseUrl();
    }

    function testShouldSetBaseUrl()
    {
        $singleton = new VF_Singleton();
        $singleton->setBaseURL('foo');
        $this->assertEquals('foo',$singleton->getBaseUrl(), 'should set base URL');
    }

    /**
     * Should throw exception if trying to get homepageSearchURL before one is set
     * @expectedException Exception
     */
    function testShouldThrowExceptionIfTryToGethomepageSearchURLBeforeOneIsSet()
    {
        $singleton = new VF_Singleton();
        $singleton->homepageSearchURL();
    }

    function testShouldSetHomepageSearchUrl()
    {
        $singleton = new VF_Singleton();
        $singleton->setHomepageSearchURL('foo');
        $this->assertEquals('foo',$singleton->homepageSearchURL(), 'should set homepage search URL');
    }
}