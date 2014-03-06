<?php

/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 *
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @author     Kyle Cannon <kyle.d.cannon@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_ServiceContainer extends Pimple implements VF_ServiceContainerInterface
{

    public function __construct(
        $schema_id,
        Zend_Controller_Request_Abstract $request,
        Zend_Db_Adapter_Abstract $read_adapter
    ) {
        parent::__construct();
        $this['schema_id'] = $schema_id;
        $this['request_class'] = $request;
        $this['read_adapter_class'] = $read_adapter;
        $this['config_class'] = function () {
            if (file_exists(ELITE_CONFIG)) {
                $zendConfig = new Zend_Config_Ini(ELITE_CONFIG, null, true);
            } else {
                $zendConfig = new Zend_Config_Ini(ELITE_CONFIG_DEFAULT, null, true);
            }
            $this->ensureDefaultSectionsExist($zendConfig);
            $config = new VF_Config($zendConfig);
            return $config;
        };

        $this['schema_class'] = function ($c) {
            return new VF_Schema($c['schema_id'], $c['read_adapter_class'], $c['config_class']);
        };

        $this['level_finder_class'] = function ($c) {
            return new VF_Level_Finder($c['schema_class'], $c['read_adapter_class'], $this['config_class']);
        };

        $this['vehicle_finder_class'] = function ($c) {
            return new VF_Vehicle_Finder($c['schema_class'], $c['read_adapter_class'], $c['config_class'], $c['level_finder_class']);
        };

        $this['flexible_search_class'] = function ($c) {
            if ($this->shouldEnableVafwheeladapterModule()) {
                return new VF_Wheeladapter_FlexibleSearch($c['schema_class'], $c['read_adapter_class'], $c['config_class'], $c['level_finder_class'], $c['vehicle_finder_class'], $c['request_class']);
            }
            if ($this->shouldEnableVafWheelModule()) {
                return new VF_Wheel_FlexibleSearch($c['schema_class'], $c['read_adapter_class'], $c['config_class'], $c['level_finder_class'], $c['vehicle_finder_class'], $c['request_class']);
            }
            if ($this->shouldEnableVaftireModule()) {
                return new VF_Tire_FlexibleSearch($c['schema_class'], $c['read_adapter_class'], $c['config_class'], $c['level_finder_class'], $c['vehicle_finder_class'], $c['request_class']);
            }
            return new VF_FlexibleSearch($c['schema_class'], $c['read_adapter_class'], $c['config_class'], $c['level_finder_class'], $c['vehicle_finder_class'], $c['request_class']);
        };

    }

    /**
     * @return Zend_Controller_Request_Abstract
     */
    public function getRequestClass()
    {
        $this->storeFitInSession();
        return $this['request_class'];
    }

    /**
     * @return Zend_Db_Adapter_Abstract
     */
    public function getReadAdapterClass()
    {
        return $this['read_adapter_class'];
    }

    /**
     * @return VF_Config
     */
    public function getConfigClass()
    {
        return $this['config_class'];
    }

    /**
     * @deprecated
     * @see VF_ServiceContainer::getFlexibleSearchClass
     */
    public function flexibleSearch()
    {
        return $this->getFlexibleSearchClass();
    }

    public function ensureDefaultSectionsExist($config)
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
        $this->ensureSectionExists($config, 'modulestatus');
    }

    public function ensureSectionExists($config, $section)
    {
        if (!is_object($config->$section)) {
            $config->$section = new VF_Config(new Zend_Config(array()));
        }
    }

    /**
     * @return VF_Schema
     */
    public function getSchemaClass()
    {
        return $this['schema_class'];
    }

    /**
     * @deprecated
     * @returns VF_FlexibleSearch|VF_Wheel_FlexibleSearch|VF_Tire_FlexibleSearch|VF_Wheeladapter_FlexibleSearch
     */
    public function getFlexibleSearch()
    {
        return $this->getFlexibleSearchClass();
    }

    /**
     * @returns VF_FlexibleSearch|VF_Wheeladapter_FlexibleSearch|VF_Wheel_FlexibleSearch|VF_Tire_FlexibleSearch
     */
    public function getFlexibleSearchClass()
    {
        return $this['flexible_search_class'];
    }


    /**
     * @return VF_Vehicle_Finder
     */
    public function getVehicleFinderClass()
    {
        return $this['vehicle_finder_class'];
    }

    /**
     * @return VF_Level_Finder||VF_Level_Finder_Selector
     */
    public function getLevelFinderClass()
    {
        return $this['level_finder_class'];
    }

    public function shouldEnableVafwheeladapterModule()
    {
        return (bool)$this->getConfigClass()->modulestatus->enableVafwheeladapter;
    }

    public function shouldEnableVafWheelModule()
    {
        return (bool)$this->getConfigClass()->modulestatus->enableVafwheel;
    }

    public function shouldEnableVaftireModule()
    {
        return (bool)$this->getConfigClass()->modulestatus->enableVaftire;
    }


    public function getProductIds()
    {
        if (isset($this->productIds) && is_array($this->productIds) && count($this->productIds)) {
            return $this->productIds;
        }
        $ids = $this->doGetProductIds();
        $this->productIds = $ids;
        return $ids;
    }

    public function doGetProductIds()
    {
        $this->storeFitInSession();
        $productIds = $this->flexibleSearch()->doGetProductIds();
        return $productIds;
    }

    public function vehicleSelection()
    {
        $this->storeFitInSession();
        $search = $this->flexibleSearch();
        return $search->vehicleSelection();
    }

    /**
     * store paramaters in the session
     *
     * @return integer fit_id
     */
    public function storeFitInSession()
    {
        return $this->getFlexibleSearchClass()->storeInSession();
    }

    function getValueForSelectedLevel($level)
    {
        $search = $this->flexibleSearch();
        $search->storeFitmentInSession();
        return $search->getValueForSelectedLevel($level);
    }

    function getFitId()
    {
        return $this->getValueForSelectedLevel($this->getSchemaClass()->getLeafLevel());
    }

}