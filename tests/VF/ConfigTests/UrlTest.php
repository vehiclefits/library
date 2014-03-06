<?php

/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 *
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @author     Kyle Cannon <kyle.d.cannon@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_ConfigTests_UrlTest extends VF_ServiceContainer_TestCase
{

    /**
     * Should throw exception if trying to get process URL before one is set
     *
     * @expectedException Exception
     */
    function testShouldThrowExceptionIfTryToGetProcessURLBeforeOneIsSet()
    {
        $this->getServiceContainer()->getConfigClass()->processUrl();
    }

    function testShouldSetProcessURL()
    {
        $config = $this->getServiceContainer()->getConfigClass();
        $config->setProcessURL('foo');
        $this->assertEquals('foo', $config->processUrl(), 'should set process URL');
    }

    /**
     * Should throw exception if trying to get base URL before one is set
     *
     * @expectedException Exception
     */
    function testShouldThrowExceptionIfTryToGetBaseURLBeforeOneIsSet()
    {
        $config = $this->getServiceContainer()->getConfigClass();
        $config->getBaseUrl();
    }

    function testShouldSetBaseUrl()
    {
        $config = $this->getServiceContainer()->getConfigClass();
        $config->setBaseURL('foo');
        $this->assertEquals('foo', $config->getBaseUrl(), 'should set base URL');
    }

    /**
     * Should throw exception if trying to get homepageSearchURL before one is set
     *
     * @expectedException Exception
     */
    function testShouldThrowExceptionIfTryToGethomepageSearchURLBeforeOneIsSet()
    {
        $config = $this->getServiceContainer()->getConfigClass();
        $config->homepageSearchURL();
    }

    function testShouldSetHomepageSearchUrl()
    {
        $config = $this->getServiceContainer()->getConfigClass();
        $config->setHomepageSearchURL('foo');
        $this->assertEquals('foo', $config->homepageSearchURL(), 'should set homepage search URL');
    }

} 