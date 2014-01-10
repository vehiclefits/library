<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 *
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Search_Level implements VF_Configurable
{
    /** @var  Zend_Config */
    protected $config;
    /** @var  VF_Search_Form */
    protected $searchForm;
    protected $level;
    protected $prevLevel;
    protected $displayBrTag;

    /**
     * Display a select box, pre-populated with values if its the first, or if there's a prev. selection.
     *
     * @param      $searchForm   VF_Search_Form
     * @param      $level        string name of the level being displayed (ex. "Model")
     * @param bool $prevLevel    name of the level preceeding this one (ex. "Make", or false if none)
     * @param null $displayBrTag boolean wether to print a <br /> between the select boxes.
     * @param null $yearRangeAlias
     *
     * @return string The rendered HTML for this select box.
     */
    function display(
        VF_Search_Form $searchForm,
        $level,
        $prevLevel = false,
        $displayBrTag = null,
        $yearRangeAlias = null
    ) {
        $this->displayBrTag = $displayBrTag;
        $this->searchForm = $searchForm;
        $this->level = $level;
        $this->prevLevel = $prevLevel;
        $this->yearRangeAlias = $yearRangeAlias;
        return $this->_display();
    }

    protected function _display()
    {
        ob_start();
        if ($this->helper()->showLabels()) {
            echo '<label>';
            echo ucfirst($this->level);
            echo ':</label>';
        }
        $prevLevelsIncluding = $this->schema()->getPrevLevelsIncluding($this->level);
        $prevLevelsIncluding = implode(',', $prevLevelsIncluding);
        ?>
        <select name="<?= $this->selectName() ?>"
                class="<?= $this->selectName() ?>Select {prevLevelsIncluding: '<?= $prevLevelsIncluding ?>'}">
            <option value="0"><?= $this->__($this->helper()->getDefaultSearchOptionText($this->level)) ?></option>
            <?php
            foreach ($this->getEntities() as $entity) {
                /** @var VF_Level $entity */
                if ($this->getConfig()->search->legacyNumericSearch):
                    ?>
                    <option
                        value="<?= $entity->getId() ?>" <?=
                    ($this->isLevelSelected($entity) ? ' selected="selected"' : '') ?>><?= $entity->getTitle(
                        ) ?></option>
                <?php else: ?>
                    <option
                        value="<?= $entity->getTitle() ?>" <?=
                    ($this->isLevelSelected($entity) ? ' selected="selected"' : '') ?>><?= $entity->getTitle(
                        ) ?></option>
                <?php endif; ?>

            <?php
            }
            ?>
        </select>
        <?php
        if ($this->displayBrTag()) {
            echo '<br />';
        }
        return ob_get_clean();
    }

    function selectName()
    {
        if ($this->yearRangeAlias) {
            return $this->yearRangeAlias;
        }
        return str_replace(' ', '_', $this->level);
    }

    function schema()
    {
        return new VF_Schema();
    }

    /**
     * Check if an entity is the selected one for this 'level'
     *
     * @param VF_Level $levelObject - level to check if is selected
     *
     * @return bool if this is the one that is supposed to be currently selected
     */
    function isLevelSelected($levelObject)
    {
        if ($this->level != $this->leafLevel()) {
            return (bool)($levelObject->getId() == $this->searchForm->getSelected($this->level));
        }
        VF_Singleton::getInstance()->setRequest($this->searchForm->getRequest());
        $currentSelection = VF_Singleton::getInstance()->vehicleSelection();
        if (false === $currentSelection) {
            return false;
        }
        if ('year_start' == $this->yearRangeAlias) {
            return (bool)($levelObject->getTitle() == $this->earliestYearInVehicles($currentSelection));
        } else {
            if ('year_end' == $this->yearRangeAlias) {
                return (bool)($levelObject->getTitle() == $this->latestYearInVehicles($currentSelection));
            }
        }
        $level = false;
        if (is_array($currentSelection) && count($currentSelection) == 1) {
            $firstVehicle = $currentSelection[0];
            /** @var VF_Vehicle $firstVehicle */
            $level = $firstVehicle->getLevel($this->leafLevel());
        } elseif ($currentSelection instanceof VF_Vehicle) {
            $level = $currentSelection->getLevel($this->leafLevel());
        }
        if ($level) {
            return (bool)($levelObject->getTitle() == $level->getTitle());
        }
    }

    function latestYearInVehicles($vehicles)
    {
        $latestYear = null;
        foreach ($vehicles as $vehicle) {
            if (is_null($latestYear) || $vehicle->getLevel('year')->getTitle() > $latestYear) {
                $latestYear = $vehicle->getLevel('year')->getTitle();
            }
        }
        return $latestYear;
    }

    function earliestYearInVehicles($vehicles)
    {
        $earliestYear = null;
        foreach ($vehicles as $vehicle) {
            if (is_null($earliestYear) || $vehicle->getLevel('year')->getTitle() < $earliestYear) {
                $earliestYear = $vehicle->getLevel('year')->getTitle();
            }
        }
        return $earliestYear;
    }

    protected function getEntities()
    {
        $search = $this->searchForm;
        if ($this->prevLevel) {
            return $search->listEntities($this->level);
        }
        return $search->listEntities($this->level);
    }

    protected function leafLevel()
    {
        return $this->schema()->getLeafLevel();
    }

    protected function displayBrTag()
    {
        if (is_bool($this->displayBrTag)) {
            return $this->displayBrTag;
        }
        return VF_Singleton::getInstance()->displayBrTag();
    }

    protected function __($text)
    {
        return $this->searchForm->translate($text);
    }

    protected function helper()
    {
        return VF_Singleton::getInstance();
    }

    function getConfig()
    {
        if (!$this->config instanceof Zend_Config) {
            $this->config = $this->helper()->getConfig();
        }
        return $this->config;
    }

    function setConfig(Zend_Config $config)
    {
        $this->config = $config;
    }
}