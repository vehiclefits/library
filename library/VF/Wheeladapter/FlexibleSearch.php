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
class VF_Wheeladapter_FlexibleSearch extends VF_Wheel_FlexibleSearch implements VF_FlexibleSearch_Interface
{
    function doGetProductIds()
    {
        if (!$this->filteringOnWheelSide() && !$this->filteringOnVehicleSide()) {
            return parent::doGetProductIds();
        }
        $finder = new VF_Wheeladapter_Finder($this->getReadAdapter());
        if ($this->filteringOnWheelSide() && $this->filteringOnVehicleSide()) {
            $productIds = $finder->getProductIds($this->wheelBolt(), $this->vehicleBolt());
        } else if (!$this->filteringOnWheelSide()) {
            $productIds = $finder->getProductIds(null, $this->vehicleBolt());
        } else if (!$this->filteringOnVehicleSide()) {
            $productIds = $finder->getProductIds($this->wheelBolt(), null);
            if ($this->hasRequest()) {
                $productIds = array_intersect($productIds, parent::doGetProductIds());
            }
        }
        if (array() == $productIds) {
            return array(0);
        }
        return $productIds;
    }

    function filteringOnWheelSide()
    {
        return $this->wheelSideLugCount() && $this->wheelSideStudSpread();
    }

    function filteringOnVehicleSide()
    {
        return $this->vehicleSideLugCount() && $this->vehicleSideStudSpread();
    }

    function wheelBolt()
    {
        $wheelBoltString = $this->wheelSideLugCount() . 'x' . $this->wheelSideStudSpread();
        return VF_Wheel_BoltPattern::create($wheelBoltString);
    }

    function vehicleBolt()
    {
        $vehicleBoltString = $this->vehicleSideLugCount() . 'x' . $this->vehicleSideStudSpread();
        return VF_Wheel_BoltPattern::create($vehicleBoltString);
    }

    function storeInSession()
    {
        $this->storeAdapterSizeInSession();
        return parent::storeInSession();
    }

    function storeAdapterSizeInSession()
    {
        if ($this->shouldClearWheelAdapterFromSession()) {
            $this->clearWheelAdapterSelectionFromSession();
            return;
        }

        $_SESSION['wheel_lug_count'] = $this->wheelSideLugCount();
        $_SESSION['wheel_stud_spread'] = $this->wheelSideStudSpread();
        $_SESSION['vehicle_lug_count'] = $this->vehicleSideLugCount();
        $_SESSION['vehicle_stud_spread'] = $this->vehicleSideStudSpread();
    }

    function shouldClearWheelAdapterFromSession()
    {
        return 0 == (int)$this->wheelSideLugCount() && 0 == (int)$this->wheelSideStudSpread() &&
        0 == (int)$this->vehicleSideLugCount() && 0 == (int)$this->vehicleSideStudSpread();
    }

    function clearWheelAdapterSelectionFromSession()
    {
        unset($_SESSION['wheel_lug_count']);
        unset($_SESSION['wheel_stud_spread']);
        unset($_SESSION['vehicle_lug_count']);
        unset($_SESSION['vehicle_stud_spread']);
    }

    function wheelSideLugCount()
    {
        return $this->getParam('wheel_lug_count');
    }

    function wheelSideStudSpread()
    {
        return $this->getParam('wheel_stud_spread');
    }

    function vehicleSideLugCount()
    {
        return $this->getParam('vehicle_lug_count');
    }

    function vehicleSideStudSpread()
    {
        return $this->getParam('vehicle_stud_spread');
    }
}