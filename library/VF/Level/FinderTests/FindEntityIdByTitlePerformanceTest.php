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
class VF_Level_FinderTests_FindEntityIdByTitlePerformanceTest extends VF_TestCase
{
    function testRootLevel()
    {
        $originalVehicle = $this->createMMY('Honda');
        $this->startProfiling();

        $finder = new VF_Level_Finder;
        $makeId = $finder->findEntityIdByTitle('make', 'Honda');
        $makeId = $finder->findEntityIdByTitle('make', 'Honda');
        $this->assertEquals(1, $this->getQueryCount());
    }

    function testNonRootLevel()
    {
        $originalVehicle = $this->createMMY('Honda', 'Civic');
        $this->startProfiling();

        $finder = new VF_Level_Finder;
        $modelId = $finder->findEntityIdByTitle('model', 'Civic', $originalVehicle->getValue('make'));
        $modelId = $finder->findEntityIdByTitle('model', 'Civic', $originalVehicle->getValue('make'));
        $this->assertEquals(1, $this->getQueryCount());
    }

    function startProfiling()
    {
        $this->getReadAdapter()->getProfiler()->clear();
        $this->getReadAdapter()->getProfiler()->setEnabled(true);
    }

    function getQueryCount()
    {
        $queries = $this->getReadAdapter()->getProfiler()->getQueryProfiles();
        return count($queries);
    }
}