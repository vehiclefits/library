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
class VF_SearchForm implements VF_Configurable
{

    /** @var Zend_Controller_Request_Http */
    protected $_request;

    /** @var Zend_Config */
    protected $config;

    protected $current_category_id;

    protected $template;

    /**
     * @var VF_FlexibleSearch
     */
    protected $flexibleSearch;

    function getProductId()
    {
        return 0;
    }

    function getRequest()
    {
        return $this->_request;
    }

    /** for testability */
    function setRequest($request)
    {
        $this->_request = $request;
    }

    function getSelected($level)
    {
        $search = new VF_FlexibleSearch($this->getSchema(), $this->getRequest());
        return $search->getValueForSelectedLevel($level);
    }

    function listEntities($level)
    {
        if (!in_array($level, $this->getSchema()->getLevels())) {
            throw new VF_Level_Exception_InvalidLevel('Invalid level [' . $level . ']');
        }
        $parent_id = 0;
        $parentLevel = $this->getSchema()->getPrevLevel($level);
        if ($parentLevel) {
            $parent_id = $this->getSelected($parentLevel);
        }
        $levelObject = new VF_Level($level);
        if ($this->isNotRootAndHasNoParent($level, $parent_id)) {
            return array();
        }
        if (!$parentLevel || !$parent_id) {
            if ($this->shouldListAll()) {
                return $levelObject->listAll();
            } else {
                return $levelObject->listInUse(array());
            }
        }
        if ($this->shouldListAll()) {
            return $levelObject->listAll($parent_id);
        } else {
            return $levelObject->listInUse($this->getFlexibleSearch()->getLevelAndValueForSelectedPreviousLevels($level));
        }
    }

    function shouldListAll()
    {
        return $this->getConfig()->search->showAllOptions;
    }

    function isNotRootAndHasNoParent($level, $parent_id)
    {
        return $this->getSchema()->getRootLevel() != $level && $parent_id == 0;
    }

    /** @return array */
    function getRequestLevels()
    {
        $levels = array();
        $displayLevels = $this->getLevels();
        foreach ($displayLevels as $level) {

            $val = $this->getFlexibleSearch()->getValueForSelectedLevel($level);
            if (!is_null($val)) {
                $levels[$level] = $val;
            }
        }
        return $levels;
    }

    function getSubmitText()
    {
        return $this->translate('Search');
    }

    function getLevels()
    {
        $schema = new VF_Schema();
        $schema->setConfig($this->getConfig());
        return $schema->getLevels();
    }

    function showClearButton()
    {
        if (isset($this->getConfig()->search->clearButton) && 'hide' === $this->getConfig()->search->clearButton) {
            return false;
        }
        return true;
    }

    function showSearchButton()
    {
        if (isset($this->getConfig()->search->searchButton) && 'hide' === $this->getConfig()->search->searchButton) {
            return false;
        }
        return true;
    }

    function clearButton()
    {
        if ('link' === $this->getConfig()->search->clearButton) {
            return 'link';
        }
        return 'button';
    }

    function searchButton()
    {
        if ('link' === $this->getConfig()->search->searchButton) {
            return 'link';
        }
        return 'button';
    }

    function getMethod()
    {
        return 'GET';
    }

    function proxyValues()
    {
        ob_start();
        $ignore = array('category', 'submitb', 'q', 'category1', 'category2', 'category3', 'category4');
        $ignore = array_merge($ignore, $this->getLevels());
        foreach ($this->getRequest()->getParams() as $key => $value) {
            if (is_string($key) && is_string($value) && !in_array($key, $ignore)) {
                echo '<input type="hidden" name="' . $this->htmlEscape($key) . '" value="' . $this->htmlEscape($value) . '" />';
            }
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    echo '<input type="hidden" name="' . $this->htmlEscape($key) . '[' . $this->htmlEscape($k) . ']" value="' . $this->htmlEscape($v) . '" />';
                }
            }
        }
        return ob_get_clean();
    }

    function getFlexibleDefinition()
    {
        return $this->getFlexibleSearch()->getFlexibleDefinition();
    }

    function shouldShowMyGarageActive()
    {
        return VF_Singleton::getInstance()->getConfig()->mygarage->collapseAfterSelection &&
        $this->getFlexibleDefinition() !== false &&
        $this->formId() == 'vafForm';
    }

    function getClearText()
    {
        return $this->translate('Clear');
    }

    function translate($text)
    {
        return $text;
    }

    /**
     * @return VF_FlexibleSearch
     */
    function getFlexibleSearch()
    {
        if(!$this->flexibleSearch) {
            $this->flexibleSearch = new VF_FlexibleSearch($this->getSchema(), $this->getRequest());
        }
        return $this->flexibleSearch;
    }

    function renderCategoryOptions()
    {
        ob_start();
        if ($this->showAllOptionOnCategoryChooser()) {
            ?>
            <option value="?"><?= $this->htmlEscape($this->getCategoryChooserAllOptionText()) ?></option>
        <?php
        }
        foreach ($this->getCategories() as $category) {
            ?>
            <option value="<?= $category['url'] ?>"><?= $category['title'] ?></option>
        <?php
        }
        return ob_get_clean();
    }

    function getSchema()
    {
        $schema = new VF_Schema();
        $schema->setConfig($this->getConfig());
        return $schema;
    }

    function renderBefore()
    {
        if (file_exists(ELITE_PATH . '/Vaflogo')) {
            $block = new Elite_Vaflogo_Block_Logo;
            return $block->_toHtml();
        }
    }

    function formId()
    {
        return 'vafForm';
    }

    function loadingStrategy()
    {
        return $this->getConfig()->search->loadingStrategy;
    }

    function unavailableSelections()
    {
        return $this->getConfig()->search->unavailableSelections;
    }

    function getConfig()
    {
        if (!$this->config instanceof Zend_Config) {
            $this->config = VF_Singleton::getInstance()->getConfig();
        }
        return $this->config;
    }

    function setConfig(Zend_Config $config)
    {
        $this->config = $config;
    }

    function url($url)
    {
        return $url;
    }

    function htmlEscape($text)
    {
        return htmlentities($text);
    }

    function setTemplate($template)
    {
        $this->template = $template;
    }

    function getTemplate()
    {
        return $this->template;
    }
}