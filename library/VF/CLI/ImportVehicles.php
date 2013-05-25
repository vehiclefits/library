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
class VF_CLI_ImportVehicles
{
    protected $opt;

    function __construct()
    {
        # Define the command line arguments this tool accepts
        $this->opt = new Zend_Console_Getopt(array(
            'file|f=s'    => 'file to import',
            'config|c=s' => 'PHP config file to initialize with',
            'force'    => 'force creation without prompting to delete old schema',
            'levels|l=s'    => 'levels to create',
            'add|a' => 'add schema instead of replace',
        ));
    }

    function main()
    {
        $file = $this->opt->getOption('file');

        $writer = new Zend_Log_Writer_Stream('vehicles-list-import.csv.log');
        $log = new Zend_Log($writer);

        $importer = new VF_Import_VehiclesList_CSV_Import($file);
        $importer->setLog($log);

        $importer->import();
    }
}