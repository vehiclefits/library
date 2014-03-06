<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Singleton
{
    static $instance;

    /**  @var Zend_Config */
    protected $config;
    /** @var  Zend_Db_Adapter_Abstract */
    protected $dbAdapter;
    protected $productIds;
    protected $_request;
    /** @var  VF_FlexibleSearch */
    protected $flexibleSearch;

    /** @return VF_Singleton */
    static function getInstance($new = false) // test only
    {
        static $instance;
        if (is_null($instance) || $new) {
            $instance = new VF_Singleton;
        }
        return $instance;
    }

    static function reset()
    {
        self::$instance = null;
    }

    function getBaseUrl($https = null)
    {
        if (isset($this->base_url)) {
            return $this->base_url;
        }
        throw new Exception('base URL has not been injected into the singleton');
    }

    function setBaseURL($url)
    {
        $this->base_url = $url;
    }

    function processUrl()
    {
        if (isset($this->process_url)) {
            return $this->process_url;
        }
        throw new Exception('process URL has not been injected into the singleton');
    }

    function setProcessURL($url)
    {
        $this->process_url = $url;
    }

    function homepageSearchURL()
    {
        if (isset($this->homepagesearch_url)) {
            return $this->homepagesearch_url;
        }
        throw new Exception;
    }

    function setHomepagesearchURL($url)
    {
        $this->homepagesearch_url = $url;
    }

}
