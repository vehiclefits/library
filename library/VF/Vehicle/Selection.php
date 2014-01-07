<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Vehicle_Selection
{
    public $vehicleParamsSelected;

    function __construct($vehicleParams = array())
    {
        $this->vehicleParamsSelected = $vehicleParams;
    }

    function earliestYear()
    {
        return $this->vehicleParamsSelected['year_start'];
    }

    function latestYear()
    {
        return $this->vehicleParamsSelected['year_end'];
    }

    function contains($vehicleParamsToCheck)
    {
        foreach($this->vehicleParamsSelected as $levelName => $selectedParam) {
            if(isset($vehicleParamsToCheck[$levelName]) && $vehicleParamsToCheck[$levelName] !== $selectedParam) {
                return false;
            }
        }
        return true;
    }

}