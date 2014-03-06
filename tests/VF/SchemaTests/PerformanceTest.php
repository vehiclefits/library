<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_SchemaTests_PerformanceTest extends VF_TestCase
{

    function doSetUp()
    {
        parent::doSetUp();
    }

    function testUsesOneQuery()
    {
        $this->getReadAdapter()->getProfiler()->clear();
        $this->getReadAdapter()->getProfiler()->setEnabled(true);
        $schema = $this->getServiceContainer()->getSchemaClass();
        $this->assertEquals(array('make', 'model', 'year'), $schema->getLevels(), 'should get levels MMY');
        $queries = $this->getReadAdapter()->getProfiler()->getQueryProfiles();
        $this->assertEquals(1, count($queries));
    }

    function testUsesOneQueryOnMultipleCalls()
    {
        $this->getReadAdapter()->getProfiler()->clear();
        $this->getReadAdapter()->getProfiler()->setEnabled(true);
        $schema = $this->getServiceContainer()->getSchemaClass();
        $schema->getLevels();
        $schema->getLevels();
        $queries = $this->getReadAdapter()->getProfiler()->getQueryProfiles();
        $this->assertEquals(1, count($queries));
    }

    function testOneQueryAcrossInstances()
    {
        $this->getReadAdapter()->getProfiler()->clear();
        $this->getReadAdapter()->getProfiler()->setEnabled(true);
        $schema = $this->getServiceContainer()->getSchemaClass();
        $schema->getLevels();
        $schema = $this->getServiceContainer()->getSchemaClass();
        $schema->getLevels();
        $queries = $this->getReadAdapter()->getProfiler()->getQueryProfiles();
        $this->assertEquals(1, count($queries));
    }
}
