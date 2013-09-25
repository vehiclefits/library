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
 *
 * @copyright  Copyright (c) 2013 Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Singleton implements VF_Configurable
{
    static $instance;

    /**  @var Zend_Config */
    protected $config;
    /** @var  Zend_Db_Adapter_Abstract */
    protected $dbAdapter;
    protected $productIds;
    protected $_request;

    /** @return VF_Singleton */
    static function getInstance($new = false) // test only
    {
        if (is_null(self::$instance) || $new) {
            self::$instance = new VF_Singleton;
        }
        return self::$instance;
    }

    function getConfig()
    {
        if (!$this->config instanceof Zend_Config) {
            if (file_exists(ELITE_CONFIG)) {
                $config = new Zend_Config_Ini(ELITE_CONFIG, null, true);
            } else {
                $config = new Zend_Config_Ini(ELITE_CONFIG_DEFAULT, null, true);
            }
            $this->setConfig($config);
        }
        return $this->config;
    }

    function setConfig(Zend_Config $config)
    {
        $this->ensureDefaultSectionsExist($config);
        $this->config = $config;
    }

    /**
     * store paramaters in the session
     *
     * @return integer fit_id
     */
    function storeFitInSession()
    {
        return $this->flexibleSearch()->storeFitInSession();
    }

    function clearSelection()
    {
        $this->flexibleSearch()->clearSelection();
    }

    function getLeafLevel()
    {
        $schema = new VF_Schema();
        return $schema->getLeafLevel();
    }

    function getValueForSelectedLevel($level)
    {
        $search = new VF_FlexibleSearch($this->schema(), $this->getRequest());
        $search->storeFitInSession();
        return $search->getValueForSelectedLevel($level);
    }

    function getFitId()
    {
        return $this->getValueForSelectedLevel($this->getLeafLevel());
    }

    function hasAValidSessionRequest()
    {
        return isset($_SESSION[$this->getLeafLevel()]) && $_SESSION[$this->getLeafLevel()];
    }

    /** @return Zend_Controller_Request_Abstract */
    function getRequest()
    {
        // get dependency injection request
        if ($this->_request instanceof Zend_Controller_Request_Abstract) {
            return $this->_request;
        }

        // get Prestashop request
        if (defined('_PS_VERSION_')) {
            return new Zend_Controller_Request_Http;
        }
    }


    function setRequest(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
    }

    function vehicleSelection()
    {
        $this->storeFitInSession();
        $search = $this->flexibleSearch();
        return $search->vehicleSelection();
    }

    function getProductIds()
    {
        if (isset($this->productIds) && is_array($this->productIds) && count($this->productIds)) {
            return $this->productIds;
        }
        $ids = $this->doGetProductIds();
        $this->productIds = $ids;
        return $ids;
    }

    function doGetProductIds()
    {
        $this->storeFitInSession();
        $productIds = $this->flexibleSearch()->doGetProductIds();
        return $productIds;
    }

    /** Get the option loading text for the ajax */
    function getLoadingText()
    {
        return isset($this->getConfig()->search->loadingText) ? $this->getConfig()->search->loadingText : 'loading';
    }

    /** Get the option text prompting the user to make a selection */
    function getDefaultSearchOptionText($level = null, $config = null)
    {
        if (is_null($config)) {
            $config = $this->getConfig();
        }
        $text = trim($config->search->defaultText);
        if (empty($text)) {
            $text = '-please select-';
        }
        $text = sprintf($text, ucfirst($level));
        return $text;
    }

    function showSearchButton()
    {
        $block = new VF_Search();
        $block->setConfig($this->getConfig());
        return $block->showSearchButton();
    }

    /** @return boolean wether or not to prefix select boxes with a label */
    function showLabels()
    {
        if (isset($this->getConfig()->search->labels)) {
            return $this->getConfig()->search->labels;
        }
        return true;
    }

    function ensureDefaultSectionsExist($config)
    {
        $this->ensureSectionExists($config, 'category');
        $this->ensureSectionExists($config, 'categorychooser');
        $this->ensureSectionExists($config, 'mygarage');
        $this->ensureSectionExists($config, 'homepagesearch');
        $this->ensureSectionExists($config, 'search');
        $this->ensureSectionExists($config, 'seo');
        $this->ensureSectionExists($config, 'product');
        $this->ensureSectionExists($config, 'logo');
        $this->ensureSectionExists($config, 'directory');
        $this->ensureSectionExists($config, 'importer');
        $this->ensureSectionExists($config, 'tire');
    }

    function ensureSectionExists($config, $section)
    {
        if (!is_object($config->$section)) {
            $config->$section = new Zend_Config(array());
        }
    }

    /** @return Zend_Db_Adapter_Abstract */
    function getReadAdapter()
    {
        if (!isset($this->dbAdapter)) {
            throw new Exception('No database adapter is set');
        }
        return $this->dbAdapter;
    }

    /** @param Zend_Db_Adapter_Abstract */
    function setReadAdapter(Zend_Db_Adapter_Abstract $dbAdapter)
    {
        $dbAdapter->getConnection()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $dbAdapter->getConnection()->query('SET character set utf8;');
        $dbAdapter->getConnection()->query('SET character_set_client = utf8;');
        $dbAdapter->getConnection()->query('SET character_set_results = utf8;');
        $dbAdapter->getConnection()->query('SET character_set_connection = utf8;');
        $dbAdapter->getConnection()->query('SET character_set_database = utf8;');
        $dbAdapter->getConnection()->query('SET character_set_server = utf8;');
        $this->dbAdapter = $dbAdapter;
    }

    function displayBrTag()
    {
        if (is_null($this->getConfig()->search->insertBrTag)) {
            return true;
        }
        return $this->getConfig()->search->insertBrTag;
    }

    function enableDirectory()
    {
        if (!is_null($this->getConfig()->directory->enable) && $this->getConfig()->directory->enable) {
            return true;
        }
        return false;
    }

    function schema()
    {
        $schema = new VF_Schema();
        return $schema;
    }

    /** @return VF_FlexibleSearch */
    function flexibleSearch()
    {
        $search = new VF_FlexibleSearch($this->schema(), $this->getRequest());
        $search->setConfig($this->getConfig());

        return $search;
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
