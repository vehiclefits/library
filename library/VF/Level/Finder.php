<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Level_Finder extends VF_Level_Finder_Abstract
{
    static function getInstance()
    {
        static $finder;
        if (is_null($finder)) {
            $finder = new VF_Level_Finder();
        }
        return $finder;
    }

    function find($level, $id)
    {
        return $this->selector()->find($level, $id);
    }

    /** @return VF_Level */
    function findEntityByTitle($type, $title, $parent_id = 0)
    {
        return $this->selector()->findEntityByTitle($type, $title, $parent_id);
    }

    /** @return integer ID */
    function findEntityIdByTitle($type, $title, $parent_id = 0)
    {
        return $this->selector()->findEntityIdByTitle($type, $title, $parent_id);
    }

    /**
     * @param mixed VF_Level|string name of level type
     * @param mixed $parent_id
     */
    function listAll($level, $parent_id = 0)
    {
        if (is_string($level)) {
            $level = new VF_Level($level, null, $this->schema);
        }
        return $this->selector()->listAll($level, $parent_id);
    }

    function __call($name, $arguments)
    {
        return call_user_func_array(array($this->selector(), $name), $arguments);
    }

    function selector()
    {
        return new VF_Level_Finder_Selector($this->schema);
    }
}