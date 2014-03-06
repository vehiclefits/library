<?php

/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 *
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @author     Kyle Cannon <kyle.d.cannon@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_ServiceContainer_FlexibleSearchTest extends VF_ServiceContainer_TestCase
{

    public function testFlexibleSearchHasCorrectSchemaForBothInstances()
    {
        $this->assertSame($this->container1->getSchemaClass(), $this->container1->getFlexibleSearchClass()->schema());
        $this->assertSame($this->container2->getSchemaClass(), $this->container2->getFlexibleSearchClass()->schema());
    }


    public function testFlexibleSearchModuleStatus_Wheeladapter()
    {
        $helper = $this->getHelperWithNewServiceContainer(
            array(
                'modulestatus' => array(
                    'enableVafwheeladapter' => false,
                    'enableVafwheel'        => true,
                    'enableVaftire'         => true
                )
            )
        );
        $this->assertFalse($helper->shouldEnableVafwheeladapterModule());
    }

    public function testFlexibleSearchModuleStatus_Wheel()
    {
        $helper = $this->getHelperWithNewServiceContainer(
            array(
                'modulestatus' => array(
                    'enableVafwheeladapter' => true,
                    'enableVafwheel'        => false,
                    'enableVaftire'         => true
                )
            )
        );
        $this->assertFalse($helper->shouldEnableVafWheelModule());
    }

    public function testFlexibleSearchModuleStatus_Tire()
    {
        $helper = $this->getHelperWithNewServiceContainer(
            array(
                'modulestatus' => array(
                    'enableVafwheeladapter' => true,
                    'enableVafwheel'        => true,
                    'enableVaftire'         => false
                )
            )
        );
        $this->assertFalse($helper->shouldEnableVaftireModule());
    }

    public function testFlexibleWheeladapterSearch_ClassInheritance()
    {
        $helper = $this->getHelperWithNewServiceContainer(
            array(
                'modulestatus' => array(
                    'enableVafwheeladapter' => true,
                    'enableVafwheel'        => false,
                    'enableVaftire'         => false
                )
            )
        );
        $this->assertInstanceOf('VF_Wheeladapter_FlexibleSearch', $helper->getFlexibleSearchClass());
        $this->assertInstanceOf('VF_Wheel_FlexibleSearch', $helper->getFlexibleSearchClass());
        $this->assertInstanceOf('VF_Tire_FlexibleSearch', $helper->getFlexibleSearchClass());
        $this->assertInstanceOf('VF_FlexibleSearch', $helper->getFlexibleSearchClass());

    }


    public function testFlexibleWheelSearch_ClassInheritance()
    {
        $helper = $this->getHelperWithNewServiceContainer(
            array(
                'modulestatus' => array(
                    'enableVafwheeladapter' => false,
                    'enableVafwheel'        => true,
                    'enableVaftire'         => false
                )
            )
        );
        $this->assertNotInstanceOf('VF_Wheeladapter_FlexibleSearch', $helper->getFlexibleSearchClass());
        $this->assertInstanceOf('VF_Wheel_FlexibleSearch', $helper->getFlexibleSearchClass());
        $this->assertInstanceOf('VF_Tire_FlexibleSearch', $helper->getFlexibleSearchClass());
        $this->assertInstanceOf('VF_FlexibleSearch', $helper->getFlexibleSearchClass());
    }

    public function testFlexibleTireSearch_ClassInheritance()
    {
        $helper = $this->getHelperWithNewServiceContainer(
            array(
                'modulestatus' => array(
                    'enableVafwheeladapter' => false,
                    'enableVafwheel'        => false,
                    'enableVaftire'         => true
                )
            )
        );
        $this->assertInstanceOf('VF_Tire_FlexibleSearch', $helper->getFlexibleSearchClass());
        $this->assertInstanceOf('VF_FlexibleSearch', $helper->getFlexibleSearchClass());
    }

    public function testFlexibleSearch_HasNoInheritanceWhenModulesAreDisabled()
    {
        $helper = $this->getHelperWithNewServiceContainer(
            array(
                'modulestatus' => array(
                    'enableVafwheeladapter' => false,
                    'enableVafwheel'        => false,
                    'enableVaftire'         => false
                )
            )
        );
        $this->assertNotInstanceOf('VF_Wheeladapter_FlexibleSearch', $helper->getFlexibleSearchClass());
        $this->assertNotInstanceOf('VF_Wheel_FlexibleSearch', $helper->getFlexibleSearchClass());
        $this->assertNotInstanceOf('VF_Tire_FlexibleSearch', $helper->getFlexibleSearchClass());
        $this->assertInstanceOf('VF_FlexibleSearch', $helper->getFlexibleSearchClass());

    }

    public function testFlexibleWheelAndTireSearch_WillBeEnabledBecauseWheelAdapterDependency()
    {
        $helper = $this->getHelperWithNewServiceContainer(
            array(
                'modulestatus' => array(
                    'enableVafwheeladapter' => true,
                    'enableVafwheel'        => false,
                    'enableVaftire'         => false
                )
            )
        );
        $this->assertInstanceOf('VF_Wheeladapter_FlexibleSearch', $helper->getFlexibleSearchClass());
        $this->assertInstanceOf('VF_Wheel_FlexibleSearch', $helper->getFlexibleSearchClass());
        $this->assertInstanceOf('VF_Tire_FlexibleSearch', $helper->getFlexibleSearchClass());
        $this->assertInstanceOf('VF_FlexibleSearch', $helper->getFlexibleSearchClass());

    }
}