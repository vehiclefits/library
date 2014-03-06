<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class VF_Vehicle_FinderTests_TestCase extends VF_TestCase
{
    protected function getFinder(VF_ServiceContainer $container = null)
    {
        if (is_null($container)) {
            return new VF_Vehicle_Finder($this->getServiceContainer()->getSchemaClass(), $this->getServiceContainer()
                ->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass(), $this->getServiceContainer()
                ->getLevelFinderClass());
        }
        return new VF_Vehicle_Finder($container->getSchemaClass(), $container->getReadAdapterClass(
        ), $container->getConfigClass(), $container->getLevelFinderClass());
    }
}