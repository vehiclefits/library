<?php


class VF_TestCase extends VF_AbstractTestCase
{
    protected function setUp()
    {

        VF_Singleton::getInstance(true);
        VF_Singleton::getInstance()->setRequest(new Zend_Controller_Request_Http);

        VF_Singleton::getInstance()->setReadAdapter($this->getTestDbAdapter());

        VF_Schema::$levels = null;

        $_SESSION = array();
        $_GET = array();
        $_REQUEST = array();
        $_POST = array();
        $_FILES = array();

        $this->resetIdentityMaps();
        $this->dropAndRecreateMockProductTable();

        $this->doSetUp();
    }


    protected function getTestDbAdapter()
    {
        return new VF_TestDbAdapter(array(
                                         'dbname'   => VAF_DB_NAME,
                                         'username' => VAF_DB_USERNAME,
                                         'password' => VAF_DB_PASSWORD
                                    ));
    }

    protected function setRequest(Zend_Controller_Request_Abstract $request)
    {
        VF_Singleton::getInstance()->setRequest($request);
    }

    /** @return Zend_Db_Adapter_Abstract */
    protected function getReadAdapter()
    {
        $adapter = VF_Singleton::getInstance()->getReadAdapter();
        return $adapter;
    }

    protected function getHelper($config = array(), $requestParams = array())
    {
        $request = $this->getRequest($requestParams);
        $helper = VF_Singleton::getInstance();
        $helper->setRequest($request);
        if (count($config)) {
            $helper->setConfig(new Zend_Config($config, true));
        }
        return $helper;
    }
}