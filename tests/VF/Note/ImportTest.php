<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Note_ImportTest extends VF_TestCase
{

    function testImportCode()
    {
        $csvData = "code,message
code1,message1
";
        $csvFile = TEMP_PATH . '/notes-definitions.csv';
        file_put_contents($csvFile, $csvData);
        $import = new VF_Note_Import($csvFile, $this->getServiceContainer()->getSchemaClass(
        ), $this->getServiceContainer()->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass(
        ), $this->getServiceContainer()->getLevelFinderClass(), $this->getServiceContainer()->getVehicleFinderClass());
        $csv = $import->import();
        $finder = $this->noteFinder();
        $actual = $finder->getAllNotes();
        $this->assertEquals('code1', $actual[0]->code, 'should be able to import note definitions code');
    }

    function testImportMessage()
    {
        $csvData = "code,message
code1,message1
";
        $csvFile = TEMP_PATH . '/notes-definitions.csv';
        file_put_contents($csvFile, $csvData);
        $import = new VF_Note_Import($csvFile, $this->getServiceContainer()->getSchemaClass(
        ), $this->getServiceContainer()->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass(
        ), $this->getServiceContainer()->getLevelFinderClass(), $this->getServiceContainer()->getVehicleFinderClass());
        $csv = $import->import();
        $finder = $this->noteFinder();
        $actual = $finder->getAllNotes();
        $this->assertEquals('message1', $actual[0]->message, 'should be able to import note definitions code');
    }

    function testImportUpdatesCode()
    {
        $csvData = "code,message
code1,message1
";
        $csvFile = TEMP_PATH . '/notes-definitions.csv';
        file_put_contents($csvFile, $csvData);
        $import = new VF_Note_Import($csvFile, $this->getServiceContainer()->getSchemaClass(
        ), $this->getServiceContainer()->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass(
        ), $this->getServiceContainer()->getLevelFinderClass(), $this->getServiceContainer()->getVehicleFinderClass());
        $csv = $import->import();
        $csvData = "code,message
code1,message-new
";
        $csvFile = TEMP_PATH . '/notes-definitions.csv';
        file_put_contents($csvFile, $csvData);
        $import = new VF_Note_Import($csvFile, $this->getServiceContainer()->getSchemaClass(
        ), $this->getServiceContainer()->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass(
        ), $this->getServiceContainer()->getLevelFinderClass(), $this->getServiceContainer()->getVehicleFinderClass());
        $csv = $import->import();
        $finder = $this->noteFinder();
        $actual = $finder->getAllNotes();
        $this->assertEquals('code1', $actual[0]->code, 'should be able to update note code with importer');
    }

    function testImportUpdatesMessage()
    {
        $csvData = "code,message
code1,message1
";
        $csvFile = TEMP_PATH . '/notes-definitions.csv';
        file_put_contents($csvFile, $csvData);
        $import = new VF_Note_Import($csvFile, $this->getServiceContainer()->getSchemaClass(
        ), $this->getServiceContainer()->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass(
        ), $this->getServiceContainer()->getLevelFinderClass(), $this->getServiceContainer()->getVehicleFinderClass());
        $csv = $import->import();
        $csvData = "code,message
code1,message-new
";
        $csvFile = TEMP_PATH . '/notes-definitions.csv';
        file_put_contents($csvFile, $csvData);
        $import = new VF_Note_Import($csvFile, $this->getServiceContainer()->getSchemaClass(
        ), $this->getServiceContainer()->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass(
        ), $this->getServiceContainer()->getLevelFinderClass(), $this->getServiceContainer()->getVehicleFinderClass());
        $csv = $import->import();
        $finder = $this->noteFinder();
        $actual = $finder->getAllNotes();
        $this->assertEquals('message-new', $actual[0]->message, 'should be able to update note message with importer');
    }
}