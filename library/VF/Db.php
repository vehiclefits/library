<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Db
{
    function getReadAdapter()
    {
        return VF_Singleton::getInstance()->getReadAdapter();
    }

    function getWriteAdapter()
    {
        return VF_Singleton::getInstance()->getWriteAdapter();
    }
}