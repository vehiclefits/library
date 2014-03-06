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
class VF_FlexibleSearch extends VF_AbstractFinderRequest implements VF_FlexibleSearch_Interface
{
    /** @var  VF_Vehicle_Finder */
    protected $vehicleSelection;
    protected $fitmentInSession;


    function getLevel()
    {
        // multi tree integration
        if ($this->getRequest()->getParam('fit')) {
            return $this->schema->getLeafLevel();
        }
        if (!$this->hasGETRequest() && !$this->hasSESSIONRequest()) {
            return false;
        }
        $last = false;
        foreach ($this->schema->getLevels() as $level) {
            if ($this->hasGETRequest() && !$this->requestingGETLevel($level)) {
                break;
            }
            if ($this->hasSESSIONRequest() && !$this->requestingSESSIONLevel($level)) {
                break;
            }
            $last = $level;
        }
        return $last;
    }

    function hasRequest()
    {
        return $this->hasGETRequest() || $this->hasSESSIONRequest();
    }

    function hasSESSIONRequest()
    {
        foreach ($this->schema->getLevels() as $level) {
            if ($this->requestingSESSIONLevel($level)) {
                return true;
            }
        }
        return false;
    }

    function hasGETRequest()
    {
        foreach ($this->schema->getLevels() as $level) {
            if ($this->requestingGETLevel($level)) {
                return true;
            }
        }
        return false;
    }

    function shouldClear()
    {
        $shouldClear = true;
        foreach ($this->schema->getLevels() as $level) {
            if ('0' !== (string)$this->getRequest()->getParam($level)) {
                $shouldClear = false;
            }
        }
        return $shouldClear;
    }

    protected function getId()
    {
        // multi tree integration
        if ($fit = $this->getRequest()->getParam('fit')) {
            return $fit;
        }
        $level = $this->getLevel();
        if ($this->request->getParam($level)) {
            return $this->request->getParam($level);
        }
        if (isset($_SESSION[$level])) {
            return $_SESSION[$level];
        }
    }

    function vehicleSelection()
    {
        if ($this->shouldClear()) {
            $this->clearVehicleSelection();
            return array();
        }

        // Multi-tree (admin panel) integration
        if ($this->request->getParam('fit')) {
            $id = $this->getId();
            if (!$id) {
                return false;
            }
            return $this->vehicleFinder->findByLevel($this->getLevel(), $id);
        }
        if (!$this->hasGETRequest() && !$this->hasSESSIONRequest()) {
            return array();
        }
        // front-end lookup
        try {
            $params = $this->vehicleRequestParams();
            if (isset($params['year_start']) && isset($params['year_end'])) {
                $vehicles = $this->vehicleFinder->findByRangeIds($params);
            } else {
                $vehicles = $this->vehicleFinder->findByLevelIds($params, VF_Vehicle_Finder::INCLUDE_PARTIALS);
            }
            return $vehicles;
        } catch (VF_Exception_DefinitionNotFound $e) {
            return false;
        }
    }

    function isNumericRequest()
    {
        $return = true;
        foreach ($this->getRequestValues() as $val) {
            if (!$val) {
                continue;
            }
            if (!is_int($val) && !ctype_digit($val)) {
                $return = false;
            }
        }
        return $return;
    }

    function vehicleRequestParams()
    {
        $requestParams = $this->request->getParams();
        $return = array();
        foreach ($this->schema->getLevels() as $level) {
            if (isset($_SESSION[$level . '_start']) && isset($_SESSION[$level . '_end'])) {
                $return[$level . '_start'] = $_SESSION[$level . '_start'];
                $return[$level . '_end'] = $_SESSION[$level . '_end'];
            } else {
                if (isset($_SESSION[$level]) && (int)$_SESSION[$level]) {
                    $return[$level] = $_SESSION[$level];
                } else {
                    if (isset($requestParams[$level . '_start']) && isset($requestParams[$level . '_end'])) {
                        $return[$level . '_start'] = $requestParams[$level . '_start'];
                        $return[$level . '_end'] = $requestParams[$level . '_end'];
                    } else {
                        if (isset($requestParams[$level]) && (int)$requestParams[$level]) {
                            $return[$level] = $requestParams[$level];
                        } else {
                            $return[$level] = 0;
                        }
                    }
                }
            }
        }
        return $return;
    }

    function requestingLevel($level)
    {
        if ($this->hasGETRequest()) {
            return (bool)$this->requestingGETLevel($level);
        }
        return (bool)$this->requestingSESSIONLevel($level);
    }

    function requestingGETLevel($level, $zeroIsValid = false)
    {
        $val = $this->request->getParam($level);
        if (!$zeroIsValid && !$val) {
            return false;
        }
        return (bool)($val);
    }

    function requestingSESSIONLevel($level)
    {
        return isset($_SESSION[$level]) && (int)$_SESSION[$level];
    }

    function getParamFromSession($param)
    {
        if (isset($_SESSION[$param]) && $this->shouldStoreInSession()) {
            return $_SESSION[$param];
        }
        return null;
    }

    function getParam($param)
    {
        if (null == $value = $this->getRequest()->getParam($param)) {
            return $this->getParamFromSession($param);
        }
        return $value;
    }

    function getRequestValues()
    {
        $values = array();
        foreach ($this->schema->getLevels() as $level) {
            if ($this->getRequest()->getParam($level . '_start') && $this->getRequest()->getParam($level . '_end')) {
                $values[$level . '_start'] = $this->getRequest()->getParam($level . '_start');
                $values[$level . '_end'] = $this->getRequest()->getParam($level . '_end');
            } elseif ('loading' == $this->getRequest()->getParam($level)) {
                continue;
            } else {
                $values[$level] = $this->getRequest()->getParam($level);
            }
        }
        return $values;
    }

    function getRequestLeafValue()
    {
        $values = $this->getRequestValues();
        return $values[$this->schema->getLeafLevel()];
    }

    public function doesLevelHaveDefaultLoadingTextAsValue($level) {
        return $this->getConfig()->getLoadingText() == $this->getRequest()->getParam($level);
    }

    function getValueForSelectedLevel($level)
    {
        // multi tree integration
        if ($fit = $this->getRequest()->getParam('fit')) {
            return $fit;
        }
        if (!$this->hasGETRequest() && isset($_SESSION[$level])) {
            return $_SESSION[$level];
        }
        if (!$this->getRequest()->getParam($level) || $this->doesLevelHaveDefaultLoadingTextAsValue($level)) {
            return false;
        }
        if ($this->isNumericRequest()) {
            return $this->getRequest()->getParam($level);
        } else {
            $levelStringValue = $this->getRequest()->getParam($level);
            $parentLevel = $this->schema()->getPrevLevel($level);
            if ($parentLevel) {
                $parentValue = $this->getValueForSelectedLevel($parentLevel);
            } else {
                $parentValue = null;
            }
            return $this->getLevelFinder()->findEntityIdByTitle(
                $level,
                $levelStringValue,
                isset($parentValue) ? $parentValue : null
            );
        }
        return false;
    }

    function getLevelAndValueForSelectedPreviousLevels($currentLevel)
    {
        $return = array();
        foreach ($this->schema->getPrevLevels($currentLevel) AS $level) {
            $return[$level] = $this->getValueForSelectedLevel($level);
        }
        return $return;
    }

    protected function buildDbSelectForDistinctEntityIdMapping()
    {
        return $this->getReadAdapter()->select()->distinct()->from($this->schema()->mappingsTable(), 'entity_id');
    }

    function buildDistinctQueryForVehicleEntityIdMappings(Zend_Db_Select $select, VF_Vehicle $vehicle)
    {
        $subSelect = $this->getReadAdapter()->select();
        foreach ($this->schema()->getLevels() AS $current_level) {
            $level = $vehicle->getLevel($current_level);
            if (!$level->getId()) {
                continue;
            }
            $subSelect->where(sprintf("%s = ?", $current_level), $level->getTitle());
        }

        $select = $this->combineSubSelectIfWhereIsNotEmpty($select, $subSelect);

        return $select;
    }

    function combineSubSelectIfWhereIsNotEmpty(Zend_Db_Select $select, Zend_Db_Select $subSelect)
    {
        $whereArray = $subSelect->getPart(Zend_Db_Select::WHERE);
        if (count($whereArray)) {
            return $select->orWhere(implode(' ', $whereArray));
        }
        return $select;
    }

    function getMappedEntityIdsForVehicle(VF_Vehicle $vehicle)
    {
        $select = $this->buildDbSelectForDistinctEntityIdMapping();
        $select = $select->where('universal = ?', 1);
        $select = $this->buildDistinctQueryForVehicleEntityIdMappings($select, $vehicle);
        return $select->query()->fetchAll(Zend_Db::FETCH_COLUMN, 0);
    }

    function doGetProductIds()
    {
        $selectedVehicles = $this->vehicleSelection();
        if (!$vehicleCount = count($selectedVehicles)) {
            return array();
        }
        if ($selectedVehicles instanceof VF_Vehicle) {
            return $this->getMappedEntityIdsForVehicle($selectedVehicles);
        }

        $select = $this->buildDbSelectForDistinctEntityIdMapping();
        $select = $select->where('universal = ?', 1);
        $subSelect = $this->getReadAdapter()->select();
        foreach ($selectedVehicles AS $vehicle) {
            $subSelect = $this->buildDistinctQueryForVehicleEntityIdMappings($subSelect, $vehicle);
        }

        $select = $this->combineSubSelectIfWhereIsNotEmpty($select, $subSelect);

        $rows = $select->query()->fetchAll(Zend_Db::FETCH_COLUMN, 0);

        if (count($rows) == 0) {
            return array(0);
        }
        return $rows;
    }

    function storeInSession()
    {
        return $this->storeFitmentInSession();
    }

    function storeFitmentInSession()
    {
        if (!$this->fitmentInSession) {
            return $this->fitmentInSession = $this->doStoreFitmentInSession();
        }
        return $this->fitmentInSession;
    }

    /**
     * store paramaters in the session
     *
     * @todo Refactor Elite_Vafgarage module section
     *
     * @return integer fit_id
     */
    function doStoreFitmentInSession()
    {
        if (!$this->shouldStoreInSession()) {
            return;
        }
        if ($this->hasGETRequest()) {
            foreach ($this->schema()->getLevels() as $level) {
                if ($this->getValueForSelectedLevel($level . '_start')) {
                    $_SESSION[$level . '_start'] = $this->getValueForSelectedLevel($level . '_start');
                    $_SESSION[$level . '_end'] = $this->getValueForSelectedLevel($level . '_end');
                } else {
                    $_SESSION[$level] = $this->getValueForSelectedLevel($level);
                }
            }
            if (file_exists(ELITE_PATH . '/Vafgarage')) {
                if (!isset($_SESSION['garage'])) {
                    $_SESSION['garage'] = new Elite_Vafgarage_Model_Garage;
                }
                $_SESSION['garage']->addVehicle($this->getRequestValues());
            }
            $leafVal = $this->getValueForSelectedLevel($this->schema()->getLeafLevel());
            if ($leafVal) {
                return $leafVal;
            }
        }
        if ($this->shouldClear()) {
            $this->clearVehicleSelection();
            return;
        }
        return $this->getValueForSelectedLevel($this->getSchema()->getLeafLevel());
    }

    function shouldStoreInSession()
    {
        if (null == $this->getConfig()) {
            return true;
        }
        if (!isset($this->getConfig()->search->storeVehicleInSession)) {
            return true;
        }
        if ('' == $this->getConfig()->search->storeVehicleInSession) {
            return false;
        }
        if ('false' == $this->getConfig()->search->storeVehicleInSession) {
            return false;
        }
        if ((bool)$this->getConfig()->search->storeVehicleInSession) {
            return true;
        }
        return false;
    }

    function getFlexibleDefinition()
    {
        $this->storeFitmentInSession();
        try {
            $level = $this->getLevel();
            $selectedVehicles = $this->vehicleSelection();
            if (!count($selectedVehicles)) {
                return false;
            }
            $selectedVehicle = $selectedVehicles[0];
            $levelObj = $selectedVehicle->getLevel($level);
            if (!$level || !$levelObj || !$levelObj->getId()) {
                return false;
            }
            $vehicle = $this->vehicleFinder->findOneByLevelIds($this->vehicleRequestParams());
        } catch (VF_Exception_DefinitionNotFound $e) {
            return false;
        }
        return $vehicle;
    }

    function clearVehicleSelection()
    {
        foreach ($this->getSchema()->getLevels() as $level) {
            if (isset($_SESSION[$level])) {
                unset($_SESSION[$level]);
            }
        }
    }
}