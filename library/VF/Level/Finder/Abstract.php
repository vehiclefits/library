<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Level_Finder_Abstract extends VF_Base implements VF_Configurable
{
    /** @var VF_Level_IdentityMap */
    protected $identityMap;

    /** @var Zend_Config */
    protected $config;
    /** @var VF_Schema */
    protected $schema;
    /** @var Zend_Db_Adapter_Abstract */
    protected $readAdapter;

    function identityMap()
    {
        if (is_null($this->identityMap)) {
            $this->identityMap = new VF_Level_IdentityMap;
        }
        return $this->identityMap;
    }

    function getTable($table)
    {
        return 'elite_level_' . $this->getSchema()->id() . '_' . str_replace(' ', '_', $table);
    }
}