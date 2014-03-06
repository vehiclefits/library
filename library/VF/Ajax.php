<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Ajax extends VF_AbstractFinderRequest implements VF_Configurable
{
    protected $legacyNumericSearch;

    function execute()
    {
        $this->legacyNumericSearch = (bool)$this->getConfig()->search->legacyNumericSearch;
        $levels = $this->getSchema()->getLevels();
        $c = count($levels);
        if (isset($_GET['front'])) {
            $product = isset($_GET['product']) ? $_GET['product'] : null;
            if (!$this->legacyNumericSearch) {
                if ($this->shouldListAll()) {
                    $levelsToSelect = array($this->requestLevel());
                    $where = $this->requestLevels();
                    $vehicles = $this->getVehicleFinder()->findDistinct($levelsToSelect, $where);
                    $children = array();
                    foreach($vehicles as $vehicle) {
                        /** @var VF_Vehicle $vehicle */
                        array_push($children, $vehicle->getLevel($this->requestLevel()));
                    }
                } else {

                    $children = $this->getLevelFinder()->listInUseByTitle(
                        new VF_Level($this->getSchema(), $this->getReadAdapter(), $this->getConfig(
                        ), $this->getLevelFinder(), $this->requestLevel(), 0),
                        $this->requestLevels(),
                        $product
                    );
                }
            } else {
                if ($this->shouldListAll()) {
                    $children = $this->getLevelFinder()->listAll(
                        new VF_Level($this->getSchema(), $this->getReadAdapter(), $this->getConfig(
                        ), $this->getLevelFinder(), $this->requestLevel(), 0),
                        $this->requestLevels(),
                        $product
                    );
                } else {
                    $children = $this->getLevelFinder()->listInUse(
                        new VF_Level($this->getSchema(), $this->getReadAdapter(), $this->getConfig(
                        ), $this->getLevelFinder(), $this->requestLevel(), 0),
                        $this->requestLevels(),
                        $product
                    );
                }
            }
        } else {
            $children = $this->getLevelFinder()->listAll($this->requestLevel(), $this->requestLevels());
        }
        echo $this->renderChildren($children);
    }

    function shouldListAll()
    {
        return $this->getConfig()->search->showAllOptions;
    }

    function requestLevel()
    {
        return $this->getRequest()->getParam('requestLevel');
    }

    function getValue($level)
    {
        return isset($_GET[$level]) ? $_GET[$level] : null;
    }

    /** Get the option text prompting the user to make a selection */
    function getDefaultSearchOptionText($level = null)
    {
        if (!isset($_GET['front'])) {
            return false;
        }
        return $this->getConfig()->getDefaultSearchOptionText($level, $this->getConfig());
    }

    function renderChildren($children)
    {
        ob_start();
        $label = $this->getDefaultSearchOptionText($this->requestLevel());
        if (count($children) > 1 && $label) {
            echo '<option value="0">' . $label . '</option>';
        }
        foreach ($children as $child) {
            if (!$this->legacyNumericSearch && isset($_GET['front'])) {
                echo '<option value="' . $child->getTitle() . '">' . htmlentities($child->getTitle(), ENT_QUOTES, 'UTF-8') . '</option>';
            } else {
                echo '<option value="' . $child->getId() . '">' . htmlentities($child->getTitle(), ENT_QUOTES, 'UTF-8') . '</option>';
            }
        }
        return ob_get_clean();
    }

    function requestLevels()
    {
        $params = array();
        foreach ($this->getSchema()->getLevels() as $level) {
            if ($this->getRequest()->getParam($level)) {
                $params[$level] = $this->getRequest()->getParam($level);
            }
        }
        return $params;
    }

}