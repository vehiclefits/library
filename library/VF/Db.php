<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class VF_Db
{
    /** @var \Zend_Db_Adapter_Abstract */
    protected $adapter;

    public function __construct(Zend_Db_Adapter_Abstract $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return Zend_Db_Adapter_Abstract
     */
    public function getReadAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param $sql
     *
     * @return Zend_Db_Statement_Interface
     */
    public function query($sql)
    {
        return $this->getReadAdapter()->query($sql);
    }
}