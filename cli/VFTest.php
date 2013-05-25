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
class VFTest extends VF_TestCase
{
    function doSetUp()
    {
        $this->switchSchema('make,model,year');
    }

    function testShouldShowUsage()
    {
        exec(__DIR__.'/vf', $output);
        $this->assertEquals(1,preg_match('#Usage vf#',$output[0]), 'should show usage if called with no command');
    }

    function testShouldImport()
    {
        $data = "make,model,year\n";
        $data .= "Honda,Civic,2000";
        file_put_contents('test.csv',$data);
        $command = __DIR__.'/vf importvehicles --config=cli/config.default.php --file=test.csv';
        passthru($command);
        $this->assertTrue($this->vehicleExists(array('make'=>'Honda','model'=>'Civic','year'=>2000)), 'should import');
    }
}