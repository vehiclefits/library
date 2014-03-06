<?php

/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 *
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @author     Kyle Cannon <kyle.d.cannon@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_ServiceContainer_SchemaTest extends VF_ServiceContainer_TestCase
{

    public function testSchemaIdIsPassedIntoSchemaClass()
    {
        $this->assertEquals(1, $this->container1->getSchemaClass()->id());
        $this->assertEquals(2, $this->container2->getSchemaClass()->id());
    }

} 