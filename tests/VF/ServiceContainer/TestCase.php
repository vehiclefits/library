<?php

/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 *
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @author     Kyle Cannon <kyle.d.cannon@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_ServiceContainer_TestCase extends VF_TestCase
{
    /**
     * @var Zend_Controller_Request_HttpTestCase
     */
    protected $request;
    /**
     * @var VF_ServiceContainer
     */
    protected $container1;
    /**
     * @var VF_ServiceContainer
     */
    protected $container2;

    public function setUp()
    {
        parent::setUp();
        $this->request = new Zend_Controller_Request_HttpTestCase();
        $this->container1 = new VF_ServiceContainer(1, $this->request, $this->getReadAdapter());
        $this->container2 = new VF_ServiceContainer(2, $this->request, $this->getReadAdapter());
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->request, $this->container1, $this->container2);
    }

} 